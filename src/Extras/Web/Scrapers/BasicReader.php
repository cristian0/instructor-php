<?php
namespace Cognesy\Instructor\Extras\Web\Scrapers;

use Cognesy\Instructor\Extras\Web\Contracts\CanGetUrlContent;

class BasicReader implements CanGetUrlContent
{
    public function getContent(string $url, array $options = []): string {
        return file_get_contents($url);
    }
}