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
     * @var string
     */
    private string $template;

    /**
     * @var array
     */
    private array $donnees;

    /**
     * Constructeur
     *
     * @param string $template
     * @param array $donnees
     */
    public function __construct(string $template, array $donnees)
    {
        $this->template = $template;
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
        if (empty($this->template)) {
            throw new Exception('Aucun template n\a été trouvé.');
        }
        include_once $this->template;
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
