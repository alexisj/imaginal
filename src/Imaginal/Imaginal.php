<?php

namespace Imaginal;

class Imaginal
{
    private $srcImage;
    private $srcW;
    private $srcH;
    private $dstImage;
    private $dir;
    private $filename;
    private $ext;

    public function __construct($dir, $filename)
    {
        if (!extension_loaded('gd'))
            throw new \Exception('The GD library is not enabled.');

        if (!is_dir($dir))
            throw new \Exception($dir.' is not a directory.');

        if (!is_file($dir.'/'.$filename))
            throw new \Exception($dir.'/'.$filename.' is not a file.');

        $ext = strtolower(substr(strrchr($filename,'.'),1));

        if ($ext === 'jpg' || $ext === 'jpeg') {
            $srcImage = imagecreatefromjpeg($dir.'/'.$filename);
        } else if ($ext === 'png') {
            $srcImage = imagecreatefrompng($dir.'/'.$filename);
        } else if ($ext === 'gif') {
            $srcImage = imagecreatefromgif($dir.'/'.$filename);
        } else {
            die('The file must be either jpeg or png or gif.');
        }

        $this->srcW = imagesx($srcImage);
        $this->srcH = imagesy($srcImage);
        $this->srcImage = $srcImage;
        $this->filename = $filename;
        $this->dir = $dir;
        $this->ext = $ext;
    }

    public function resize($width, $height)
    {
        if ($this->srcH < $this->srcW) {
            $ratio = $this->srcH / $width;
            $h = $this->srcH / $ratio;
            $w = $this->srcW / $ratio;
            $x = ($w - $width) / -2;
            $y = ($h - $height) / -2;
        } else if ($this->srcH > $this->srcW) {
            $ratio = $this->srcW / $height;
            $h = $this->srcH / $ratio;
            $w = $this->srcW / $ratio;
            $y = ($h - $height) / -2;
            $x = ($w - $width) / -2;
        } else if ($height < $width) {
            $ratio = $this->srcH / $height;
            $h = $this->srcH / $ratio;
            $w = $this->srcW / $ratio;
            $x = ($w - $width) / -2;
            $y = 0;
        } else if ($height > $width) {
            $ratio = $this->srcH / $width;
            $h = $this->srcH / $ratio;
            $w = $this->srcW / $ratio;
            $x = 0;
            $y = ($h - $height) / -2;          
        } else {
            $h = $height;
            $w = $width;
            $x = 0;
            $y = 0;
        }

        $newImage = imagecreatetruecolor($width, $height);
  
        imagecopyresampled($newImage, $this->srcImage, $x, $y, 0, 0, $w, $h, $this->srcW, $this->srcH);

        $this->dstImage = $newImage;
    }

    public function save($prefix = '')
    {
        if ($this->ext === 'png')
            return imagepng($this->dstImage, $this->dir.'/'.$prefix.$this->filename, 0);
        else if ($this->ext === 'gif')
            return imagegif($this->dstImage, $this->dir.'/'.$prefix.$this->filename);
        else
            return imagejpeg($this->dstImage, $this->dir.'/'.$prefix.$this->filename, 100);
    }

    public function saveAs($dir, $prefix = '')
    {
        if (!is_dir($dir))
            throw new \Exception($dir.' is not a directory.');

        if ($this->ext === 'png')
            return imagepng($this->dstImage, $dir.'/'.$prefix.$this->filename, 0);
        else if ($this->ext === 'gif')
            return imagegif($this->dstImage, $dir.'/'.$prefix.$this->filename);
        else
            return imagejpeg($this->dstImage, $dir.'/'.$prefix.$this->filename, 100);
    }    
}