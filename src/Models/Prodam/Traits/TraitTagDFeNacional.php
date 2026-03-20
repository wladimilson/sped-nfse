<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMElement;
use NFePHP\Common\Strings;

/**
 * @property Dom $dom
 * @property DOMElement $dFeNacional
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagDFeNacional
{
    /**
     * dFeNacional conforme tpDFeNacional em schema Prodam
     * tag <root>/IBSCBS/valores/gReeRepRes/documentos/dFeNacional
     */
    public function tagDFeNacional(stdClass $std): DOMElement
    {
        $possible = [
            'tipoChaveDFe',
            'xTipoChaveDFe',
            'chaveDFe',
        ];

        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag dFeNacional -';
        $this->dFeNacional = $this->dom->createElement('dFeNacional');

        $this->dom->addChild(
            $this->dFeNacional,
            'tipoChaveDFe',
            Strings::onlyNumbers($std->tipoChaveDFe),
            true,
            $identificador . 'Documento fiscal a que se refere a chaveDfe que seja um dos documentos do Repositório Nacional'
        );

        $this->dom->addChild(
            $this->dFeNacional,
            'xTipoChaveDFe',
            $std->xTipoChaveDFe,
            false,
            $identificador . 'Descrição da DF-e a que se refere a chaveDfe que seja um dos documentos do Repositório Nacional'
        );

        $this->dom->addChild(
            $this->dFeNacional,
            'chaveDFe',
            Strings::onlyNumbers($std->chaveDFe),
            true,
            $identificador . 'Chave do Documento Fiscal eletrônico do repositório nacional referenciado para os casos de operações já tributadas'
        );

        return $this->dFeNacional;
    }
}
