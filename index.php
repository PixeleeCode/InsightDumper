<?php

require_once './vendor/autoload.php';
require_once 'Resources/functions/in.php';

$data = [
    'niveau1' => [
        'string' => 'Ceci est une chaîne de caractères',
        'int' => 42,
        'bool' => true,
        'DateTime' => new DateTime('now'),
        'tableauImbrique' => [
            'niveau2' => [
                'float' => 3.14,
                'null' => null,
                'tableauPlusProfond' => [
                    'niveau3' => [
                        'objet' => new class {
                            public string $propriete = 'Valeur de la propriété';
                        },
                        'arrayObject' => new ArrayObject([
                            'cle' => 'valeur',
                            'autreObjet' => new DateTime('tomorrow')
                        ]),
                        'encorePlusProfond' => [
                            'niveau4' => [
                                'message' => 'Vous êtes très profond dans la structure.'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

// Pour tester la récursion
$data['niveau1']['tableauImbrique']['niveau2']['tableauPlusProfond']['niveau3']['encorePlusProfond']['niveau4']['retour'] = &$data;

in($data);
