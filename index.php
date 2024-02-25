<?php

require_once 'vendor/autoload.php';
require_once 'Resources/functions/in.php';

use Pixelee\InsightDumper\Test;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Supposons que la classe Test existe dans l'espace de noms Pixelee\InsightDumper
$testClass = new Test();
$testClass->setTestProperty('yep!');

$testClassTwo = new Test();
$testClassTwo->setTestProperty('salut toi!');

$fruits = array (
    "fruits"  => array("a" => "orange", "b" => "banana", "c" => "apple"),
    "numbers" => array(1, 2, 3, 4, 5, 6, $testClass),
    "holes"   => array("first", 5 => "second", "third", array($testClass, array($testClass, array($testClassTwo, array($testClass, array($testClassTwo))))))
);

// Un exemple de callable
$callable = function($arg) { return "Called with arg: $arg"; };
function test($arg) { return "Called with arg: $arg"; }

// Ouvrir une ressource
$resource = fopen(__FILE__, 'rb');

$datetimeimmutable = new \DateTimeImmutable('now', new DateTimeZone('UTC'));

// Passer différents types de données à la fonction "in()"
in();
in($fruits, $testClass, false, 'test', 12.5, 6, null, new DateTime('now'), $datetimeimmutable, $callable, test('ok'), $resource);

// Assurez-vous de fermer la ressource ouverte
if (is_resource($resource)) {
    fclose($resource);
}


function simpleFunction() {
    return 'Résultat de simpleFunction';
}

class MyClass {
    public static function staticMethod() {
        return 'Résultat de MyClass::staticMethod';
    }
}

class MyClassInstance {
    public function instanceMethod(): string {
        return 'Résultat de instanceMethod';
    }
}

$myClassInstance = new MyClassInstance();

$closure = function() {
    return 'Résultat de Closure';
};

class InvokableClass {
    public function __invoke(): string {
        return 'Résultat de InvokableClass';
    }
}

$invokable = new InvokableClass();


// Tests de différents callables
in(simpleFunction()); // Fonction nomée
in([MyClass::class, 'staticMethod']); // Méthode de classe statique
in([$myClassInstance, 'instanceMethod']); // Méthode d'instance
in($closure); // Fonction anonyme (Closure)
in($invokable, $myClassInstance); // Objet invocable


// Exemple de classe pour tester l'affichage des objets
class Personne {
    private string $nom;
    private string $prenom;
    public function __construct(string $nom, string $prenom) {
        $this->nom = $nom;
        $this->prenom = $prenom;
    }
}

// Classe avec méthode magique __toString
class Produit {
    private string $nom;
    public function __construct(string $nom) {
        $this->nom = $nom;
    }
    public function __toString() {
        return $this->nom;
    }

    public static function getStatic(): string
    {
        return 'static';
    }
}

// Tableau de test contenant divers types de données
$donneesDeTest = [
    'entier' => 42,
    'flottant' => 3.14,
    'chaine' => 'Bonjour le monde',
    'booleen' => true,
    'tableau' => [
        'a' => 'premier niveau',
        'b' => [
            'ba' => 'second niveau',
            'bb' => [
                'bba' => 'troisième niveau'
            ],
        ],
    ],
    'objet' => new Personne('Doe', 'John'),
    'produit' => new Produit('Chaise'),
    'datetime' => new DateTime(),
    'callable' => function() { return 'Résultat de la fonction anonyme'; },
    'null' => null,
];

in($donneesDeTest);

// Définir une classe test avec des propriétés statiques et d'instance
class TestClass {
    public static $staticProp = 'Valeur statique';
    public $instanceProp = 'Valeur d\'instance';

    public static function getStaticValue() {
        return self::$staticProp;
    }

    public function getInstanceValue() {
        return $this->instanceProp;
    }
}


$arrayObject = new ArrayObject([
    'premier' => 'Valeur 1',
    'deuxième' => 'Valeur 2',
    'troisième' => new DateTime(),
]);

$testObj = new TestClass();
$datetime = new DateTime();
$nestedArray = [
    'niveau1' => [
        'niveau2' => [
            'date' => $datetime,
            'objet' => $testObj,
            'iterable' => $arrayObject
        ]
    ]
];

// Utilisation hypothétique de la fonction in() pour afficher les informations
in($testObj, $nestedArray, $arrayObject, $arrayObject['premier'], $arrayObject->getArrayCopy());

class MonIterator implements Iterator {
    private $position = 0;
    private $array = ["premier" => "élément 1", "deuxième" => "élément 2", "troisième" => "élément 3"];

    public function __construct() {
        $this->position = 0;
    }

    #[ReturnTypeWillChange]
    public function rewind() {
        $this->position = 0;
    }

    #[ReturnTypeWillChange]
    public function current() {
        return $this->array[array_keys($this->array)[$this->position]];
    }

    #[ReturnTypeWillChange]
    public function key() {
        return array_keys($this->array)[$this->position];
    }

    #[ReturnTypeWillChange]
    public function next() {
        ++$this->position;
    }

    #[ReturnTypeWillChange]
    public function valid() {
        return isset(array_keys($this->array)[$this->position]);
    }
}

class MaCollection implements IteratorAggregate {
    private $items = [];

    public function __construct($items) {
        $this->items = $items;
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->items);
    }
}

// Test avec MonIterator
$monIterator = new MonIterator();
in($monIterator);

// Test avec MaCollection
$maCollection = new MaCollection(["un" => "Apple", "deux" => "Banana", "trois" => "Cherry"]);
in($maCollection);
