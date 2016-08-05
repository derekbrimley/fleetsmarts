<?php

class Trippak extends MY_Controller 
{
	function index()
	{
		$this->load->library('ftp');
		$config['hostname'] = 'ftp.integratedlogicsticssolutions.co';
		$config['username'] = 'covax13';
		$config['password'] = 'Retret13!';
		$config['debug']    = TRUE;

		$this->ftp->connect($config);

		$list = $this->ftp->list_files('/trippak');
		
		$zip_list = [];
		
		print_r($list);
		echo "<br>";
		foreach($list as $filename)
		{
			if ($filename != "." && $filename != ".." && strtolower(substr($filename, strrpos($filename, '.') + 1)) == 'zip')
			{
				$zip_list[] = $filename;
			}
		}
		
		print_r($zip_list);
		echo "<br>";
		
		
		foreach($zip_list as $zip_file)
		{
			$new_files = [];
			$new_file_names = [];
			
			$path = '/home/covax13/trippak/' . $zip_file;
			echo $path . "<br>";
			$zip = new ZipArchive;

			if ($zip->open($path) === true) 
			{
				
				for($i = 0; $i < $zip->numFiles; $i++) 
				{
					$filename = $zip->getNameIndex($i);
					
					echo "Filename: " . $filename . "<br>";
					
					$fileinfo = pathinfo($filename);
					
//					print_r($fileinfo);
					echo "<br>";
					
					copy("zip://".$path."#".$filename, "/home/covax13/trippak_pics/".$fileinfo['basename']);
					
					$new_files[] = "/home/covax13/trippak_pics/".$fileinfo['basename'];
					$new_file_names[] = $fileinfo['basename'];
				}
				
				$zip->close();
			}else{
				echo "false <br>";
			}
			echo "New Files: ";
			print_r($new_files);
			
			
			$path = '/home/covax13/trippak_pics/BatchInfo.xml';

			$xml = file_get_contents($path);

			$data = new SimpleXMLElement($xml);

			$trip_number = $data->Fields->TripNumber->Value;
			$truck_number = $data->Fields->TruckNumber->Value;

			$i = 1;
			foreach($new_files as $new_file){
				
				$ext = pathinfo($new_file, PATHINFO_EXTENSION);
				
				//INSERT NEW SECURE_FILE AND UPLOAD FILE TO FTP SERVER -- SIGNATURE
				$name = basename($new_file);
				if($ext == 'tif'){
					$type = "application/tif";
				}else if($ext == 'pdf'){
					$type = "application/pdf";
				}else if($ext == 'xml'){
					$type = "application/xml";
				}

				$file_guid = get_random_string(5);

				$secure_file = null;
				$secure_file['name'] = $name;
				$secure_file['type'] = $type;
				$secure_file['title'] = $name;
				$secure_file['category'] = "Trippak";
				$secure_file['file_guid'] = $file_guid;
				$secure_file['server_path'] = '/trippak_pics/';
				$secure_file['office_permission'] = 'All';
				$secure_file['driver_permission'] = 'None';
				db_insert_secure_file($secure_file);
				
				//GET NEWLY INSERTED SECURE FILE
				$where = null;
				$where["file_guid"] = $file_guid;
				$where["name"] = $name;
				$new_secure_file = db_select_secure_file($where);
				
				//UPDATE GUID WITH ID APPENDED TO BEGINNING
				$update_file = null;
				$update_file["file_guid"] = $new_secure_file["id"].$file_guid;
				
				
				if($ext == 'tif'){
					$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif";
					$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif";
				}else if($ext == 'pdf'){
					$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf";
					$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf";
				}else if($ext == 'xml'){
					$update_file["name"] = "(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml";
					$update_file["title"] = "(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml";
				}
				
				$where = null;
				$where["id"] = $new_secure_file["id"];
				db_update_secure_file($update_file,$where);

				$trippak = null;
				$trippak['load_number'] = $trip_number;
				$trippak['truck_number'] = $truck_number;
				db_insert_trippak($trippak);

				$trippak_object = db_select_trippak($trippak);
				$trippak_id = $trippak_object['id'];

				//CREATE ATTACHMENT IN DB
				$attachment = null;
				$attachment["type"] = "trippak";
				$attachment["attached_to_id"] = $trippak_id;
				$attachment["file_guid"] = $new_secure_file["id"].$file_guid;
				
				if($ext == 'tif'){
					$attachment["attachment_name"] = "Scan " . $i;
				}else if($ext == 'pdf'){
					$attachment["attachment_name"] = "Scan " . $i;
				}else if($ext == 'xml'){
					$attachment["attachment_name"] = "XML";
				}

				db_insert_attachment($attachment);
				
				if($ext == 'tif'){
					rename($new_file, "/home/covax13/trippak_pics/(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").tif");
				}else if($ext == 'pdf'){
					rename($new_file, "/home/covax13/trippak_pics/(" . $new_secure_file['id'] . ")trippakscan_" . $trip_number . "_(" . $i . ").pdf");
				}else if($ext == 'xml'){
					rename($new_file, "/home/covax13/trippak_pics/(" . $new_secure_file['id'] . ")trippakxml_" . $trip_number . ".xml");
				}
				
				$i++;
			}

			rename ("/home/covax13/trippak/" . $zip_file, "/home/covax13/trippak_pics/old_zips/" . $zip_file);
//			unlink("/home/covax13/trippak/" . $zip_file);
		}
		
		$this->ftp->close();
	}
	
}