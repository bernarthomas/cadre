<?php

namespace Cadre;

class Vue
{
    private string $cheminFichier;

    private array $templates;

    private array $donnees;

    public function __construct(string $cheminFichier, array $templates, array $donnees)
    {
        $this->cheminFichier = $cheminFichier;
        $this->donnees = $donnees;
    }

    public function affiche()
    {
        $cheminProjet = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
        ob_start();
        extract($this->donnees);
        if (empty($this->templates)) {
            throw new \Exception('Aucun template n\a été trouvé.');
        }
        foreach($this->templates as $template)
        {
            include $template;
        }
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