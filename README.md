# Images Annotation Programme
Online tool to label images for image recognition (Pascal VOC xml format).

![Screen Shot](http://bipbipavertisseur.alwaysdata.net/example/images/screen_shot_1.jpg)

Discover an example: http://bipbipavertisseur.alwaysdata.net/example/

## Customization

### Configuration
To customize the directories used, edit the PHP file **inc/configuration.php**

### Images
Images to be annotated are located in **data/images/collection_01/part_1 and data/images/collection_01/part_2**

### Annotations Target directory 
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
* jQuery-select-areas see [GitHub project](https://github.com/360Learning/jquery-select-areas)
* EasyAutocomplete see [GitHub project](https://github.com/pawelczak/EasyAutocomplete)

I modified some pieces of code to adapt the features to my needs.

## License:

Code released under the <a href='http://github.com/pawelczak/EasyAutocomplete/blob/master/LICENSE.txt' > MIT license</a>.
