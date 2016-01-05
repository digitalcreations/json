<?php

$json = file_get_contents('cards.json');
$start = microtime(true);
$data = json_decode($json);
$end = microtime(true);
$simple = $end - $start;

echo "Items: " . count($data) ."\r\n";
echo "Time elapsed: " . ($simple) . " s\r\n";

require_once('../vendor/autoload.php');

class Card {
    public $layout;
    public $name;
    public $manaCost;
    public $cmc;
    /**
     * @var string[]
     */
    public $colors;
    public $type;
    /**
     * @var string[]
     */
    public $types;
    public $text;
    public $power;
    public $toughness;
    public $imageName;
    /**
     * @var string[]
     */
    public $colorIdentity;
}

$serializer = new \DC\JSON\Serializer();
$start = microtime(true);
$data = $serializer->deserialize($json, '\Card[]');
$end = microtime(true);
$complex = $end - $start;

echo "Time elapsed: " . ($complex) . " s\r\n";
echo "Factor: " . ($complex / $simple) ."\r\n";