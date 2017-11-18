# Image Annotation Programme
Free Online web tool to annotate images, output format is a list of xml files (Pascal VOC xml format). This image labelling application will help you creating a learning base for image recognition.

![Screen Shot](http://bipbipavertisseur.alwaysdata.net/example/images/screen_shot_2.jpg)

Discover an example: http://bipbipavertisseur.alwaysdata.net/image_annotations_v2.0_d1

## Customization

### 1. Configuration
To customize the directories used, edit the PHP file **inc/configuration.php**
```php
<?php
# Image path to be used in the HTML client
$IMAGE_WEB_DIR = "data/images";

# Image path for internal PHP use
$IMAGE_ROOT_DIR  = "../data/images";
$ANNOTATIONS_DIR = "../data/annotations";

# Collection name 
$COLLECTION_NAME = "collection_01";

# Not annotated image 80% to be presented to user
$ratio_new_old = 80;
?>
```
### 2. Images
Images to be annotated are located in **data/images/collection_01/part_1** and **data/images/collection_01/part_2**

### 3. List of classes

The list of classes can be customized in the file **resources/list_of_tags.json**
```json
[
	{"name": "Long Beak Bird", "icon": "resources/tag_examples/long_beak.jpg"},
	{"name": "Eagle", "icon": "resources/tag_examples/eagle.jpg"},
	{"name": "Parrot", "icon": "resources/tag_examples/parrot.jpg"},	
	{"name": "Baby Bird", "icon": "resources/tag_examples/baby_bird.jpg"}
]
```
The result is quite cool !<br />
![Screen Shot](http://bipbipavertisseur.alwaysdata.net/example/images/list_species.jpg)

### 4. Annotations Target directory 
Each image will generate one XML file in the directory **data/annotations**

## Output as Pascal VOC xml files

This format is a standard and can be easily read from [Tensorflow Object Detection API](https://github.com/tensorflow/models/tree/master/object_detection)

```xml
<?xml version="1.0"?>
<annotation>
  <folder>collection_01/part_1</folder>
  <filename>pexels-photo-60091.jpg</filename>
  <path/>
  <source>
    <database>Unknown</database>
  </source>
  <size_part>
     <width>1125</width>
     <height>750</height>
     <depth>3</depth>
  </size_part>
  <segmented>0</segmented>
  <object>
    <name>Bird</name>
    <pose>Unspecified</pose>
    <truncated>0</truncated>
    <difficult>0</difficult>
    <bndbox>
      <xmin>488</xmin>
      <ymin>245.5</ymin>
      <xmax>674</xmax>
      <ymax>601.5</ymax>
    </bndbox>
  </object>
</annotation>
```

## Contributions

**Many thanks** to the contributors of these useful libraries:
* [jQuery-select-areas](https://github.com/360Learning/jquery-select-areas) available on GitHub
* [EasyAutocomplete](https://github.com/pawelczak/EasyAutocomplete) available on GitHub

I modified some pieces of code to adapt the features to my needs.

## License

Code released under the <a href='http://github.com/pawelczak/EasyAutocomplete/blob/master/LICENSE.txt' > MIT license</a>.
