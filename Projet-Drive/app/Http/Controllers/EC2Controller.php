<?php
 
 namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use Aws\Ec2\Ec2Client;
 
 class EC2Controller extends Controller
 {
     public function listInstances()
     {
         try {
             $ec2 = new Ec2Client([
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

             $result = $ec2->describeInstances();
             $instances = [];

             if (isset($result['Reservations'])) {
                 foreach ($result['Reservations'] as $reservation) {
                     if (isset($reservation['Instances'])) {
                         foreach ($reservation['Instances'] as $instance) {
                             $name = 'N/A';
                             if (isset($instance['Tags'])) {
                                 foreach ($instance['Tags'] as $tag) {
                                     if ($tag['Key'] === 'Name') {
                                         $name = $tag['Value'];
                                         break;
                                     }
                                 }
                             }

                             $instances[] = [
                                 'id' => $instance['InstanceId'] ?? 'N/A',
                                 'name' => $name,
                                 'type' => $instance['InstanceType'] ?? 'N/A',
                                 'state' => $instance['State']['Name'] ?? 'unknown',
                                 'public_ip' => $instance['PublicIpAddress'] ?? 'N/A',
                                 'private_ip' => $instance['PrivateIpAddress'] ?? 'N/A',
                                 'launch_time' => isset($instance['LaunchTime']) ? $instance['LaunchTime']->format('Y-m-d H:i') : 'N/A',
                                 'key_name' => $instance['KeyName'] ?? 'N/A',
                             ];
                         }
                     }
                 }
             }

             $instancesJson = json_encode($instances);
             $error = null;
         } catch (\Exception $e) {
             $instancesJson = json_encode([]);
             $error = $e->getMessage();
         }

         return view('EC2', compact('instancesJson', 'error'));
     }
    public function restartInstance($instanceId){
        try {
            $ec2 = new Ec2Client([
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

            $result = $ec2->rebootInstances([
                'InstanceIds' => [$instanceId],
            ]);
            return back()->with('success', 'Instance redémarrée avec succès !');
        }catch(\Exception $e){
            return back()->with('error', 'Erreur lors du redémarrage de l\'instance : ' . $e->getMessage());
        }
    }

    public function stopInstance($InstanceId){
        try {
            $ec2 = new Ec2Client([
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

            $result = $ec2->stopInstances([
                'InstanceIds' => [$InstanceId],
            ]);
            return back()->with('success', 'Instance arrêtée avec succès !');
        }catch(\Exception $e){
            return back()->with('error', 'Erreur lors de l\'arrêt de l\'instance : ' . $e->getMessage());
        }
    }

    public function startInstance($instanceId){
        try {
            $ec2 = new Ec2Client([
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

            $result = $ec2->startInstances([
                'InstanceIds' => [$instanceId],
            ]);
            return back()->with('success', 'Instance démarrée avec succès !');
        }catch(\Exception $e){
            return back()->with('error', 'Erreur lors du démarrage de l\'instance : ' . $e->getMessage());
        }
    }

    public function createInstance(){

    }

    public function deleteInstance($instanceId){
        try {
            $ec2 = new Ec2Client([
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

            $result = $ec2->terminateInstances([
                'InstanceIds' => [$instanceId],
            ]);
            return back()->with('success', 'Instance résiliée avec succès !');
        }catch(\Exception $e){
            return back()->with('error', 'Erreur lors de la résiliation de l\'instance : ' . $e->getMessage());
        }
    }
 }  
