<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMElement;
use NFePHP\Common\Strings;

/**
 * @property Dom $dom
 * @property DOMElement $docFiscalOutro
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagDocFiscalOutro
{
    /**
     * DocFiscalOutro conforme tpDocFiscalOutro em schema Prodam
     * tag <root>/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro
     */
    public function tagDocFiscalOutro(stdClass $std): DOMElement
    {
        $possible = [
            'cMunDocFiscal',
            'nDocFiscal',
            'xDocFiscal',
        ];

        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag docFiscalOutro -';
        $this->docFiscalOutro = $this->dom->createElement('docFiscalOutro');

        $this->dom->addChild(
            $this->docFiscalOutro,
            'cMunDocFiscal',
            Strings::onlyNumbers($std->cMunDocFiscal),
            true,
            $identificador . 'Código do município emissor do documento fiscal que não se encontra no repositório nacional'
        );

        $this->dom->addChild(
            $this->docFiscalOutro,
            'nDocFiscal',
            $std->nDocFiscal,
            true,
            $identificador . 'Número do documento fiscal que não se encontra no repositório nacional'
        );

        $this->dom->addChild(
            $this->docFiscalOutro,
            'xDocFiscal',
            $std->xDocFiscal,
            true,
            $identificador . 'Descrição do documento fiscal'
        );

        return $this->docFiscalOutro;
    }
}
