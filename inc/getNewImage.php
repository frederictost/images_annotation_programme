<?php

include 'xmlVocReadAnnotationsFile.php';
include 'configuration.php';

$service_requested = $_GET["info"];

# Search the xml file in a $dir
function getXmlFile($dir, $filename)
{
	$xml_filepath = null;
    $files = scandir($dir);
    $results = null;	
		
    foreach($files as $key => $value)
	{									
        if ( strcasecmp($value, $filename) == 0 ) 
		{
            $xml_filepath = $dir.DIRECTORY_SEPARATOR.$filename;
			return $xml_filepath;
		}
    }

    return $xml_filepath;
}

$it = new RecursiveDirectoryIterator($IMAGE_ROOT_DIR);

# List of images to process
$list_of_images = array();
$list_of_annotated_images = array();
$list_of_not_annotated_images = array();

# Index of images
$image_index = 0;
$annotated_image_index = 0;
$not_annotated_image_index = 0;

#$file = 'file.log';
#file_put_contents($file, "INFO - Start the loop\n");
	
foreach(new RecursiveIteratorIterator($it) as $file) 
{	

	# Process file
	if ( (strpos(strtoupper($file), '.JPG') !== false) && (strstr($file, $COLLECTION_NAME)) )
	{
		# echo $file . "<br>";
		$delimiter = "/";
		$item = explode($delimiter, $file);
		$nbItems = count($item);
		# Should be A/C type / MSN / Image name
		if ($nbItems>=3)
		{

			$image_name = $item[$nbItems-1];
			$msn = $item[$nbItems-2];
			$type = $item[$nbItems-3];
			$image_info = array("type" => $type, "msn" => $msn, 
			   "name" => $image_name);

			# Add the image in the list
			$list_of_images[$image_index] = $image_info;
			$image_index = $image_index + 1;			
			
			# Try to find the annotation
			$id = str_replace(array(".jpg",".JPG"),".jpg", $image_name);
			$xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $id);
			$xml_filepath = getXmlFile($ANNOTATIONS_DIR, $xml_filename);

			if ($xml_filepath != null)
			{
				$list_of_annotated_images[$annotated_image_index] = $image_info;
				$annotated_image_index = $annotated_image_index + 1;
			}
			else
			{
				$list_of_not_annotated_images[$not_annotated_image_index] = $image_info;
				$not_annotated_image_index = $not_annotated_image_index + 1;
			}
		}									
	}		
}

$file = 'file.log';
file_put_contents($file, "INFO - getNewImage.php\n");

# Show the list
/*echo "Annotated images:<br>";
foreach( $list_of_annotated_images as $image_info ) 
{
	echo $image_info["type"] . "/" . $image_info["msn"] . "/" . $image_info["name"] . "<br>";	
}

echo "<br>All images:<br>";
foreach( $list_of_images as $image_info ) 
{
	echo $image_info["type"] . "/" . $image_info["msn"] . "/" . $image_info["name"] . "<br>";	
}

echo "Number of images :" . count($list_of_images) ."<br>";*/

# New image 80%
$random_new = rand(0, 99);

file_put_contents($file, "Random index = ".$random_new."\n",FILE_APPEND | LOCK_EX);

# Not annotated 80%
if ( ($random_new < $ratio_new_old) && (count($list_of_not_annotated_images)>0))
{
	file_put_contents($file, "Not annotated 80%\n",FILE_APPEND | LOCK_EX);
	# Get a random number 
	$random_index = rand(0, count($list_of_not_annotated_images)-1);
	$image_info = $list_of_not_annotated_images[$random_index];
}
# Annotated 20%
else
{
	file_put_contents($file, "Annotated 20%\n",FILE_APPEND | LOCK_EX);
	# If exist
	if (count($list_of_annotated_images)>0)
	{
		# Get a random number 
		$random_index = rand(0, count($list_of_annotated_images)-1);
		$image_info = $list_of_annotated_images[$random_index];
	}
	else
	{
		file_put_contents($file, "Force not annotated\n",FILE_APPEND | LOCK_EX);
		# Get a random number 
		$random_index = rand(0, count($list_of_not_annotated_images)-1);
		$image_info = $list_of_not_annotated_images[$random_index];
	}
}

#	$random_index = rand(0, count($list_of_images)-1);
#	$image_info = $list_of_images[$random_index];

$url = $IMAGE_WEB_DIR."/".$image_info["type"] . "/" . $image_info["msn"] . "/" . $image_info["name"];

# Remove extension
$id = str_replace(array(".jpg",".JPG"),".jpg", $image_info["name"]);

# Get the xml file, replace .jpg by xml
$xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $id);			

# Try to find the annotation
$xml_filepath = getXmlFile($ANNOTATIONS_DIR, $xml_filename);

if ($xml_filepath != null)
{
	# echo "xml_filepath" . $xml_filepath;
	$annotations = [];
	$xml = new xmlVocReadAnnotationsFile($xml_filepath);
	
	file_put_contents($file, "xml_filepath ".$xml_filepath."\n",FILE_APPEND | LOCK_EX);
	
	if (!$xml->hasError())
	{
		file_put_contents($file, "Parse XML\n",FILE_APPEND | LOCK_EX);
		$xml->parseXML();
		if (!$xml->hasError())
		{
			$annotations = $xml->getAnnotations();
			file_put_contents($file, "Annotations ".serialize($annotations)."\n",FILE_APPEND | LOCK_EX);
		}
	}	
	else
	{
		file_put_contents($file, "An error occurs\n",FILE_APPEND | LOCK_EX);
		$annotations = [];
	}
}
else
{	
	file_put_contents($file, "No annotations found.\n",FILE_APPEND | LOCK_EX);
	$annotations = [];
}

file_put_contents($file, "Annotations ".serialize($annotations)."\n",FILE_APPEND | LOCK_EX);

file_put_contents($file, "URL image = ".$url."\n",FILE_APPEND | LOCK_EX);

# Prepare message to send
$data = array ("url" => $url, "id" => $id, "folder" => $image_info["type"] . "/" . $image_info["msn"], 
				"annotations" => $annotations);
	
file_put_contents($file, "Annotations ".serialize($data)."\n",FILE_APPEND | LOCK_EX);
	
header('Content-Type: application/json');
echo json_encode($data);

?>