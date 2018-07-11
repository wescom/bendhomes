<?php

echo "starting import";
$feedReURL = "http://www.bendbulletin.com/csp/mediapool/sites/BendBulletin/Exports/mrss.csp?publication=BendBulletin&section=realestatenews&areas=topStory,listStories1,listStories2&imgwidth=940";
$feedHGURL = "http://www.bendbulletin.com/csp/mediapool/sites/BendBulletin/Exports/mrss.csp?publication=BendBulletin&section=SPhomeandgarden&areas=topStory,listStories1,listStories2&imgwidth=940";
$feed97URL = "http://www.bendbulletin.com/csp/mediapool/sites/BendBulletin/Exports/mrss.csp?publication=BendBulletin&section=Area97&areas=topStory,listStories1,listStories2&imgwidth=940";

$urlArray = [$feedReURL, $feedHGURL, $feed97URL];
var_dump($urlArray);
foreach ($urlArray as $url)
{
        $content = file_get_contents($url);
        $x = new SimpleXmlElement($content);

        echo "<ul>";
        foreach($x->channel->item as $entry)
        {
                echo "<li>".$entry->title;

                $ns_media = $entry->children('http://search.yahoo.com/mrss/');


                if (isset($ns_media->content));
                {
                        $imgCount = 0;
                        foreach($ns_media->content as $oneImg){
                                $imgCount++;
                                $image = (string)$oneImg->attributes()['url'];

                                echo " --- ".$image." ---";
                                if ($image != "")
                                {
                                        $guid = $entry->guid;
                                        $guidArray = explode("-", $guid);
                                        $cmsId = $guidArray[0];
                                        $newName = $cmsId."_".$imgCount.".jpg";
                                        echo $newName;
                                        $ch = curl_init($image);
                                        $fileName = '/var/www/html/_retsapi/imagesStories/'.$newName;
                                        $fp = fopen($fileName, 'wb');
                                        curl_setopt($ch, CURLOPT_FILE, $fp);
                                        curl_setopt($ch, CURLOPT_HEADER, 0);
                                        curl_exec($ch);
                                        curl_close($ch);
                                        fclose($fp);
                                        $contents = file_get_contents($image);
                                        file_put_contents($fileName, $contents);
                                }
                        }
                }
                echo "</li>";

        }
        echo "</ul>";
}