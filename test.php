<?php

// replace with file to your own project bootstrap
require_once __DIR__ . '/vendor/autoload.php';

Amp\Loop::run(Amp\GreenThread\coroutine(function () {
    /** @var \Doctrine\ORM\EntityManager $entityManager */
    $entityManager = require __DIR__ . '/bootstrap.php';

    $foo = new \App\Entity\Foo();
    $foo->setName('bar');
    $entityManager->persist($foo);

    $entityManager->flush();

    [$entities1, $entities2] = \Amp\GreenThread\await([
        \Amp\GreenThread\async(function () use ($entityManager) {
            return $entityManager->getRepository(\App\Entity\Foo::class)->findAll();
        }),
        \Amp\GreenThread\async(function () use ($entityManager) {
            return $entityManager->getRepository(\App\Entity\Foo::class)->findAll();
        }),
    ]);

    var_dump($entities1[0]);
    var_dump($entities2[0]);
}));
