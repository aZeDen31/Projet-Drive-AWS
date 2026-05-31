<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Ec2\Ec2Client;

class VPCController extends Controller
{
    private function getEc2Client()
    {
        return new Ec2Client([
            'region'      => config('filesystems.disks.s3.region'),
            'version'     => 'latest',
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
            'http' => [
                'verify' => config('filesystems.disks.s3.http.verify', true),
            ],
        ]);
    }

    public function listVpcs()
    {
        try {
            $ec2 = $this->getEc2Client();
            
            $resultVpcs = $ec2->describeVpcs();
            $vpcs = [];

            if (isset($resultVpcs['Vpcs'])) {
                foreach ($resultVpcs['Vpcs'] as $vpc) {
                    $name = 'N/A';
                    if (isset($vpc['Tags'])) {
                        foreach ($vpc['Tags'] as $tag) {
                            if ($tag['Key'] === 'Name') {
                                $name = $tag['Value'];
                                break;
                            }
                        }
                    }

                    $vpcs[] = [
                        'id' => $vpc['VpcId'] ?? 'N/A',
                        'name' => $name,
                        'cidr' => $vpc['CidrBlock'] ?? 'N/A',
                        'state' => $vpc['State'] ?? 'unknown',
                        'is_default' => $vpc['IsDefault'] ?? false,
                    ];
                }
            }

            $resultSubnets = $ec2->describeSubnets();
            $subnets = [];

            if (isset($resultSubnets['Subnets'])) {
                foreach ($resultSubnets['Subnets'] as $sub) {
                    $name = 'N/A';
                    if (isset($sub['Tags'])) {
                        foreach ($sub['Tags'] as $tag) {
                            if ($tag['Key'] === 'Name') {
                                $name = $tag['Value'];
                                break;
                            }
                        }
                    }

                    $subnets[] = [
                        'id' => $sub['SubnetId'] ?? 'N/A',
                        'vpc_id' => $sub['VpcId'] ?? 'N/A',
                        'cidr' => $sub['CidrBlock'] ?? 'N/A',
                        'availability_zone' => $sub['AvailabilityZone'] ?? 'N/A',
                        'available_ip_count' => $sub['AvailableIpAddressCount'] ?? 0,
                        'name' => $name,
                        'state' => $sub['State'] ?? 'unknown',
                        'is_default' => $sub['DefaultForAz'] ?? false,
                    ];
                }
            }

            $vpcsJson = json_encode($vpcs);
            $subnetsJson = json_encode($subnets);
            $error = null;
        } catch (\Exception $e) {
            $vpcsJson = json_encode([]);
            $subnetsJson = json_encode([]);
            $error = $e->getMessage();
        }

        return view('VPC', compact('vpcsJson', 'subnetsJson', 'error'));
    }

    public function createVpc(Request $request)
    {
        $request->validate([
            'cidr_block' => 'required|string|regex:/^([0-9]{1,3}\.){3}[0-9]{1,3}\/[0-9]{1,2}$/',
            'name'       => 'nullable|string|max:50',
        ]);

        try {
            $ec2 = $this->getEc2Client();
            
            $params = [
                'CidrBlock' => $request->cidr_block,
            ];

            if ($request->filled('name')) {
                $params['TagSpecifications'] = [
                    [
                        'ResourceType' => 'vpc',
                        'Tags' => [
                            [
                                'Key'   => 'Name',
                                'Value' => $request->name,
                            ],
                        ],
                    ],
                ];
            }

            $ec2->createVpc($params);

            return back()->with('success', 'VPC créé avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création du VPC : ' . $e->getMessage());
        }
    }

    public function deleteVpc($vpcId)
    {
        try {
            $ec2 = $this->getEc2Client();
            $ec2->deleteVpc([
                'VpcId' => $vpcId,
            ]);
            return back()->with('success', 'VPC supprimé avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du VPC : ' . $e->getMessage());
        }
    }

    public function createSubnet(Request $request)
    {
        $request->validate([
            'vpc_id'            => 'required|string',
            'cidr_block'        => 'required|string|regex:/^([0-9]{1,3}\.){3}[0-9]{1,3}\/[0-9]{1,2}$/',
            'availability_zone' => 'nullable|string',
            'name'              => 'nullable|string|max:50',
        ]);

        try {
            $ec2 = $this->getEc2Client();
            
            $params = [
                'VpcId'     => $request->vpc_id,
                'CidrBlock' => $request->cidr_block,
            ];

            if ($request->filled('availability_zone')) {
                $params['AvailabilityZone'] = $request->availability_zone;
            }

            if ($request->filled('name')) {
                $params['TagSpecifications'] = [
                    [
                        'ResourceType' => 'subnet',
                        'Tags' => [
                            [
                                'Key'   => 'Name',
                                'Value' => $request->name,
                            ],
                        ],
                    ],
                ];
            }

            $ec2->createSubnet($params);

            return back()->with('success', 'Sous-réseau (Subnet) créé et associé avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création du sous-réseau : ' . $e->getMessage());
        }
    }

    public function deleteSubnet($subnetId)
    {
        try {
            $ec2 = $this->getEc2Client();
            $ec2->deleteSubnet([
                'SubnetId' => $subnetId,
            ]);
            return back()->with('success', 'Sous-réseau (Subnet) supprimé avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du sous-réseau : ' . $e->getMessage());
        }
    }
}
