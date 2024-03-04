<?php
namespace Tests;

use Cognesy\Instructor\Schema\PropertyInfoBased\Factories\FunctionCallFactory;
use Tests\Examples\Events;
use Tests\Examples\Event;

if (!function_exists('addEvent')) {
    /**
     * Function creates project event
     * @param string $title Title of the event
     * @param string $date Date of the event
     * @param \Tests\Examples\Stakeholder[] $stakeholders Stakeholders involved in the event
     * @return \Tests\Examples\Event
     */
    function addEvent(string $title, string $date, array $stakeholders): Event {
        return new Event();
    }
}


it('creates function call - object', function () {
    $array = (new FunctionCallFactory)->fromClass(Events::class, 'addEvent', 'Extract object from provided content');
    expect($array)->toBeArray();
    expect($array['type'])->toEqual('function');
    expect($array['function']['name'])->toEqual('addEvent');
    expect($array['function']['description'])->toEqual('Extract object from provided content');
    expect($array['function']['parameters']['type'])->toEqual('object');
    expect($array['function']['parameters']['properties']['events']['type'])->toEqual('array');
    expect($array['function']['parameters']['properties']['events']['items']['type'])->toEqual('object');
    // ...
    expect($array)->toMatchSnapshot();
});