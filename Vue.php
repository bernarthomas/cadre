<?php
/**
 * Classe Vue moteur de template php
 */
namespace Cadre;

use Exception;

/**
 * Classe Vue moteur de template php
 */
class Vue
{
    /**
     * Liste des templates dans leur ordre d'inclusion
     *
     * @var array
     */
    private array $templates;

    /**
     * @var array
     */
    private array $donnees;

    /**
     * Constructeur
     *
     * @param array $templates
     * @param array $donnees
     */
    public function __construct(array $templates, array $donnees)
    {
        $this->templates = $templates;
        $this->donnees = $donnees;
    }

    /**
     * Méthode d'affiche des templates avec interprétation php
     *
     * @param bool $debug
     * @throws Exception
     */
    public function affiche(bool $debug = false)
    {
        ob_start();
        extract($this->donnees);
        if (empty($this->templates)) {
            throw new Exception('Aucun template n\a été trouvé.');
        }
        foreach ($this->templates as $template) {
            include_once $template;
        }
        $codePhp = ob_get_clean();
        $codePhpModifie = str_replace(['"'], ['\"'], $codePhp);
        $motifs = ['/<.+?>/', '/{%.+?%}/', '/{{.+?}}/'];
        $remplacements = ['<?php echo "$0"; ?>', '<?php $0 ?>', '<?php echo "$0"; ?>'];
        $codePhpModifie = preg_replace($motifs, $remplacements, $codePhpModifie);
        $codePhpModifie = str_replace(['{{ ', '{%', ' }}', ' %}'], ['', '', '', ''], $codePhpModifie);
        $codePhpModifie = substr($codePhpModifie, 5);
        $codePhpModifie = substr($codePhpModifie, 0, -3);
        if (true === $debug) {
            var_dump($codePhpModifie);
        }
        eval($codePhpModifie);
    }
}
