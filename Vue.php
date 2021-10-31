<?php

namespace Cadre;

class Vue
{
    private $cheminFichier;

    private $donnees;

    public function __construct($cheminFichier, $donnees)
    {
        $this->cheminFichier = $cheminFichier;
        $this->donnees = $donnees;
    }

    public function affiche()
    {
        $cheminProjet = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
        ob_start();
        extract($this->donnees);
        include 'template/base_avant_body.phtml';
        include $cheminProjet . $this->cheminFichier;
        include 'template/base_apres_body.phtml';
        $codePhp = ob_get_clean();
        $codePhpModifie = str_replace(['"'], ['\"'], $codePhp);
        $motifs = ['/<.+?>/', '/{%.+?%}/', '/{{.+?}}/'];
        $remplacements = ['<?php echo "$0"; ?>', '<?php $0 ?>', '<?php echo "$0"; ?>'];
        $codePhpModifie = preg_replace($motifs, $remplacements, $codePhpModifie);
        $codePhpModifie = str_replace(['{{ ', '{%', ' }}', ' %}'], ['', '', '', ''], $codePhpModifie);
        $codePhpModifie = substr($codePhpModifie, 5);
        $codePhpModifie = substr($codePhpModifie, 0, -3);
//        var_dump($codePhpModifie);
        eval($codePhpModifie);
    }

}