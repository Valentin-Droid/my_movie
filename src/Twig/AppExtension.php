<?php

namespace App\Twig;

use Symfony\Config\TwigConfig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getImageUrl', [$this, 'getImageUrl']),
        ];
    }

    public function getImageUrl($path, $size = 'w500')
    {
        return 'https://image.tmdb.org/t/p/' . $size . $path;
    }
}