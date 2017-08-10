<?php

// Copied from Python code pascal_voc_io.py
class xmlVocAnnotations 
{	
	private $_foldername;
	private $_filename;
	private $_databaseSrc;
	private $_imgSize;
	private $_localImgPath;
	private $_boxlist;
	 	 
    function __construct( $foldername, $filename, $imgSize, $databaseSrc="Unknown", $localImgPath=null)
	{
        $this->_foldername = $foldername;
        $this->_filename = $filename;
        $this->_databaseSrc = $databaseSrc;
        $this->_imgSize = $imgSize;
        $this->_boxlist = [];
        $this->_localImgPath = $localImgPath;
		
		$this->_domDoc = new DOMDocument;
	}
	
    public function prettify()
	{
        $this->_domDoc->formatOutput = true;
	}
	
    public function genXML()
	{
        // 
        // Return XML root
        // 
        // Check conditions		

        if ( ($this->_filename == null) || 
                ($this->_foldername == null) || 
                ($this->_imgSize == null) || 
                (count($this->_boxlist) <= 0) )
		{
			
			return null;
		}
	
		$top = $this->_domDoc->createElement('annotation'); 						
		$topNode = $this->_domDoc->appendChild($top);
		
        $folder = $this->_domDoc->createElement('folder',$this->_foldername);		
		$folderNode = $topNode->appendChild($folder);
		
		$filename = $this->_domDoc->createElement('filename',$this->_filename);	
		$filenameNode = $topNode->appendChild($filename);
		
		$localImgPath = $this->_domDoc->createElement('path',$this->_localImgPath);	
		$localImgPathNode = $topNode->appendChild($localImgPath);       

		$source = $this->_domDoc->createElement('source');
		$sourceNode = $topNode->appendChild($source);
		
		$database = $this->_domDoc->createElement('database',$this->_databaseSrc);
		$sourceNode->appendChild($database);			
        
		$size_part = $this->_domDoc->createElement('size_part');
		$size_partNode = $topNode->appendChild($size_part);	
				
        $width  = $this->_domDoc->createElement('width',  strval($this->_imgSize['width']));
		$height = $this->_domDoc->createElement('height', strval($this->_imgSize['height']));
        $depth  = $this->_domDoc->createElement('depth',  strval($this->_imgSize['depth']));
        
		$size_partNode->appendChild($width);
		$size_partNode->appendChild($height);
		$size_partNode->appendChild($depth);

		$segmented = $this->_domDoc->createElement("segmented","0");
		$topNode->appendChild($segmented);        		
		
        return $top;
	}
	
	# Tag is $name
    public function addBndBox($xmin, $ymin, $width, $height, $name)
	{
        $bndbox = ['xmin'=>$xmin, 'ymin'=>$ymin, 'xmax'=>($xmin+$width), 'ymax'=>($ymin+$height)];
        $bndbox['name'] = $name;
		array_push($this->_boxlist, $bndbox);    	
	}

    public function appendObjects($top) 
	{		
		
		
        foreach ($this->_boxlist as &$box)
		{
            $object_item = $this->_domDoc->createElement('object');			
			$object_itemNode = $top->appendChild($object_item);			
			
			$name = $this->_domDoc->createElement('name',  $box["name"]);
			$object_itemNode->appendChild($name);
			
			$pose = $this->_domDoc->createElement('pose',  Unspecified);
			$object_itemNode->appendChild($pose);			
						
			$truncated = $this->_domDoc->createElement('truncated',  "0");
			$object_itemNode->appendChild($truncated);
			
			$difficult = $this->_domDoc->createElement('difficult',  "0");
			$object_itemNode->appendChild($difficult);			          
			
			$bndbox = $this->_domDoc->createElement('bndbox');
			$bndboxNode = $object_itemNode->appendChild($bndbox);	

			$xmin = $this->_domDoc->createElement('xmin',  $box["xmin"]);
			$bndboxNode->appendChild($xmin);

			$ymin = $this->_domDoc->createElement('ymin',  $box["ymin"]);
			$bndboxNode->appendChild($ymin);	

			$xmax = $this->_domDoc->createElement('xmax',  $box["xmax"]);
			$bndboxNode->appendChild($xmax);

			$ymax = $this->_domDoc->createElement('ymax',  $box["ymax"]);
			$bndboxNode->appendChild($ymax);			
		}		
	}
	
    public function save($targetDir)
	{
		// Generate the XML tree
		$file = 'file.log';
		file_put_contents($file, "Before genXML()\n",FILE_APPEND | LOCK_EX);	
        
		$root = $this->genXML();						
		$this->appendObjects($root);
		$this->prettify();
		              
		// Replace .jpg by .xml
		$filename = str_replace(array(".jpg",".JPG"),".xml", $this->_filename);		
		$fullPath = $targetDir. DIRECTORY_SEPARATOR . $filename;
				
		file_put_contents($file, "Save annotations to ". $fullPath ."\n",FILE_APPEND | LOCK_EX);					
		file_put_contents($file, "Xml file: ". $filename ."\n",FILE_APPEND | LOCK_EX);	
		
		$this->_domDoc->save($fullPath);
	}
	
} // End of class

/*$data_as_serialize = 'O:8:"stdClass":6:{s:3:"url";s:59:"images/collection/collection_01/famille/20150131_185559.jpg";s:2:"id";s:19:"20150131_185559.jpg";s:6:"folder";s:21:"collection_01/famille";s:5:"width";i:3264;s:6:"height";i:2448;s:11:"annotations";s:378:"[{"tag":"Anemo probe","x":1618.0064308681672,"y":391.81993569131834,"width":335.84565916398714,"height":374.32797427652736},{"tag":"Anemo probe","x":2552.0771704180065,"y":423.30546623794214,"width":279.87138263665594,"height":279.87138263665594},{"tag":"DND:Drop Nose Device","x":2782.9710610932475,"y":1224.4372990353697,"width":423.30546623794214,"height":1000.540192926045}]";}';

$obj = unserialize($data_as_serialize);

$folder = $obj->{'folder'};
$id     = $obj->{'id'};
$width  = $obj->{'width'};
$height = $obj->{'height'};
$annotations = json_decode($obj->{'annotations'},true);

$imageSize = [  "width"  => $width ,
				"height" => $height,
				"depth"  => 3 ];
$xml = new xmlVocAnnotations($folder, $id, $imageSize);

foreach ($annotations as &$annotation)
{
	$xml->addBndBox($annotation["x"],
					$annotation["y"],
					$annotation["width"],
					$annotation["height"],
					$annotation["tag"]);
}

// Write xml to file
$xml->save();*/


?>