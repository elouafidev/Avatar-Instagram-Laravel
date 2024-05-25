<?php

namespace App\Http\Classes;


use DOMDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarInstagramClass{

    private string $cookies_sesseion_id ;
    private string $htmlcontent;
    private \DOMDocument $DOM_document ;
    private string $title = '';
    private array $metadata = [];
    private String $avatar = '';
    private string $username = '';
    private string $description = '';
    private string $name= '';
    private string $followers= '';
    private string $following= '';
    private string $posts= '';
    private string $bio= '';

    public function getArray(){
        return[
            'username' => $this->username,
            'description' => $this->description,
            'name' => $this->name,
            'followers' => $this->followers,
            'following' => $this->following,
            'posts' => $this->posts,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'title' => $this->title,
            'metadata' => $this->metadata,
            'html' => $this->htmlcontent,
        ];
    }

    /**
     * @param $username string username in instagram
     */
    public function __construct($username)
    {
        $this->cookies_sesseion_id = "[ Your Instagram Cookies Session ID ]";
        $this->username = $username;
        $url ='https://www.instagram.com/'.$username.'/';
        $this->DOM_document = new DOMDocument();
        $this->DOM_document->loadHTML($this->get_https_content($url));
        //$this->DOM_document->loadHTML($this->htmlfile());

        $metasArray = [];
        $metas = $this->DOM_document->getElementsByTagName('meta');
        for($i=0; $i<$metas->length; $i++){
            $meta = $metas->item($i);
            $metasArray[$meta->getAttribute('property')] = $meta->getAttribute('content');
        }
        $this->metadata = $metasArray;

        $this->title = empty($this->metadata["og:title"])? '' : $this->metadata["og:title"];
        $this->description = empty($this->metadata["og:description"])? '' : $this->metadata["og:description"];
        $this->avatar = empty($this->metadata["og:image"])? '' : html_entity_decode($this->metadata["og:image"]);
        $this->Followers();
        $this->Following();
        $this->NameWithUsername();
        $this->bio();

    }

    function get_https_content($url,$method="GET"){
        $ch = curl_init();
        $userAgent = [
            "Gecko" =>"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20100101 Firefox/31.0",
            "GoogleBot" => "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            "GeckoMisc" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36",
            "InstgramAndroid" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0",
            "brave" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.38 Safari/537.36 Brave/75"
        ];
        $cookies = [
            "sessionid" => $this->cookies_sesseion_id
        ];
        $cookieString='';
        foreach ($cookies as $n=>$c){
            $cookieString.=$n."=".$c."; ";
        }

        $headers   = array();
        $headers[] = 'Cookie: ' . $cookieString;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent['brave']);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        $result =  curl_exec($ch);
        curl_close($ch);
        $this->htmlcontent = $result;
        //Storage::put('temp_https_content.html',$this->htmlcontent);
        return $result;
    }

    function htmlfile(){
        $html =  Storage::get('temp_https_content.html');
        /*        $html=Str::replace("\n","",$html);
                $html=Str::replace("\r","",$html);
                $html=Str::replace(" ","",$html);
                $html=Str::replace("<!DOCTYPE html>","",$html);*/
        $this->htmlcontent = $html;
        return $html;
    }

    public function html(){return $this->htmlcontent;}

    public const STATE_LOGIN = -1;
    public const STATE_UNEXIST = 0;
    public const STATE_EXIST = 1;

    /**
     * @return int|null
     */
    public function HttpsState() {
        return  empty($this->avatar) ? self::STATE_UNEXIST : self::STATE_EXIST;
    }



    public function Metadata(){

        return $this->metadata;
    }
    public function Title(){
        return $this->title;
    }
    public function Avatar(){
        return  $this->avatar;
    }
    public function Description(){
        return $this->description;
    }
    public function Followers(){
        if(empty($this->followers))
        {
            $this->followers  = $this->get_string_between(
                "#Start#".$this->Description(),
                "#Start#",
                "Followers,",
            );
        }
        return $this->followers;
    }
    public function Following(){
        if(empty($this->following))
        {
            $this->following  = $this->get_string_between(
                $this->Description(),
                'Followers,',
                'Following,',
            );
        }
        return $this->following;
    }
    public function Posts(){
        if(empty($this->posts))
        {
            $this->posts  = $this->get_string_between(
                $this->Description(),
                'Following,',
                'Posts',
            );
        }
        return $this->posts;
    }
    public function NameWithUsername(){
        if(empty($this->name))
        {
            $this->name  = $this->get_string_between(
                '#Start#'.$this->Title(),
                '#Start#',
                '• Instagram photos',
            );
        }
        return $this->name;
    }

    public function Bio(){
        return "";
        /*$this->bio = $this->get_string_between(
            $this->htmlcontent,
            "<script type=\"text/javascript\">window._sharedData =",
            "</script>"

        );*/
        $_sharedData ="";
        $scripts = $this->DOM_document->getElementsByTagName('script');
        for($i=0; $i<$scripts->length; $i++){
            $script = $scripts->item($i);
            if(str_contains($script->nodeValue,"window._sharedData")){
                $_sharedData = $script->nodeValue;
                break;
            }

        }
        $_sharedData=Str::replace("\n","",$_sharedData);
        $_sharedData=Str::replace("\r","",$_sharedData);
        $_sharedData=Str::replace(" ","",$_sharedData);
        $_sharedData=Str::replace("window._sharedData=","",$_sharedData);
        substr_replace($_sharedData,'',-1);

        return $_sharedData;
    }


    /**
     * get all script in html
     * @return array
     */
    public function script(){

        $scriptArray= [];
        $scripts = $this->DOM_document->getElementsByTagName('script');
        for($i=0; $i<$scripts->length; $i++){
            $meta = $scripts->item($i);
            $scriptArray[] = [
                'type' => $meta->getAttribute('type'),
                'content' => $meta->nodeValue
            ];
        }
        return  $scriptArray;
    }

    private function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    function get_string_index(string $string, string $start, string $end, int $index = 0)
    {
        if(false === ($c = preg_match_all('/' . preg_quote($start, '/') . '(.*?)' . preg_quote($end, '/') . '/us', $string, $matches)))
            return false;

        if($index < 0)
            $index += $c;

        return $index < 0 || $index >= $c
            ? false
            : $matches[1][$index]
            ;
    }
}
