<?php

$feedReURL = "http://www.bendbulletin.com/csp/mediapool/sites/BendBulletin/Exports/mrss.csp?publication=BendBulletin&section=realestate&areas=topStory,listStories1,listStories2&imgwidth=940";
$feedHGURL = "http://www.bendbulletin.com/csp/mediapool/sites/BendBulletin/Exports/mrss.csp?publication=BendBulletin&section=homeandgarden&areas=topStory,listStories1,listStories2&imgwidth=940";
$feedSPURL = "http://www.bendbulletin.com/csp/mediapool/sites/BendBulletin/Exports/mrss.csp?publication=BendBulletin&section=SPbendhomesfeed&areas=topStory,listStories1,listStories2&imgwidth=940";

$urlArray = [$feedReURL, $feedHGURL, $feedSPURL];

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
                        $image = (string)$ns_media->content->attributes()['url'];

                        echo " --- ".$image." ---";
                        if ($image != "")
                        {
                                $guid = $entry->guid;
                                $guidArray = explode("-", $guid);
                                $cmsId = $guidArray[0];
                                $newName = $cmsId.".jpg";
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
                echo "</li>";

        }
        echo "</ul>";
}