<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMElement;
use NFePHP\Common\Strings;

/**
 * @property Dom $dom
 * @property DOMElement $docOutro
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagDocOutro
{
    /**
     * DocOutro conforme tpDocOutro em schema Prodam
     * tag <root>/IBSCBS/valores/gReeRepRes/documentos/docOutro
     */
    public function tagDocOutro(stdClass $std): DOMElement
    {
        $possible = [
            'nDoc',
            'xDoc',
        ];

        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag docOutro -';
        $this->docOutro = $this->dom->createElement('docOutro');

        $this->dom->addChild(
            $this->docOutro,
            'nDoc',
            $std->nDoc,
            true,
            $identificador . 'Número do documento não fiscal'
        );

        $this->dom->addChild(
            $this->docOutro,
            'xDoc',
            $std->xDoc,
            true,
            $identificador . 'Descrição do documento não fiscal'
        );

        return $this->docOutro;
    }
}
