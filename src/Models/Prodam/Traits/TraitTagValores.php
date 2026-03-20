<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved;
use stdClass;
use DOMElement;

/**
 * @property DOMImproved $dom
 * @property DOMElement $valures
 * @method equilizeParameters($std, $possible)
 * @method conditionalNumberFormatting($value, $decimal = 2)
 */

trait TraitTagValores
{
    use TraitTagEnd;
    use TraitTagDFeNacional;
    use TraitTagDocFiscalOutro;
    use TraitTagDocOutro;
    use TraitTagDest;

    /**
     * Informações relacionadas aos valores do serviço prestado para IBS e à CBS
     * tag <root>/IBSCBS/valores
     * NOTA: Ajustado para NFS-e v2
     */
    protected function tagValores(): DOMElement
    {
        $this->valores = $this->dom->createElement("valores");
        return $this->valores;
    }

    /**
     * Grupo de informações relativas a valores incluídos neste documento e recebidos por 
	 * motivo de estarem relacionadas a operações de terceiros, objeto de reembolso, repasse ou 
	 * ressarcimento pelo recebedor, já tributados e aqui referenciados.
     * tag <root>/IBSCBS/valores/gReeRepRes/documentos[]
     */
    public function tagDocumentos(stdClass $std): DOMElement
    {
        $possible = [
            'dtEmiDoc',
            'dtCompDoc',
            'tpReeRepRes',
            'xTpReeRepRes',
            'vlrReeRepRes',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag gReeRepRes/documentos -';

        if (!isset($this->gReeRepRes)) {
            $this->gReeRepRes = $this->dom->createElement('gReeRepRes');
        }

        $documentos = $this->dom->createElement("documentos");

        if (!empty($std->dFeNacional)) {
            $dfeStd = is_array($std->dFeNacional) ? (object) $std->dFeNacional : $std->dFeNacional;
            $this->dom->appChild(
                $documentos,
                $this->tagDFeNacional($dfeStd),
                'Tipo de documento do repositório nacional'
            );
        } elseif (!empty($std->docFiscalOutro)) {
            $docFiscalStd = is_array($std->docFiscalOutro) ? (object) $std->docFiscalOutro : $std->docFiscalOutro;
            $this->dom->appChild(
                $documentos,
                $this->tagDocFiscalOutro($docFiscalStd),
                'Grupo de informações de documento fiscais, eletrônicos ou não, que não se encontram no repositório nacional'
            );
        } elseif (!empty($std->docOutro)) {
            $docOutroStd = is_array($std->docOutro) ? (object) $std->docOutro : $std->docOutro;
            $this->dom->appChild(
                $documentos,
                $this->tagDocOutro($docOutroStd),
                'Grupo de informações de documento não fiscal'
            );
        }

        if (!empty($std->fornec)) {
            $fornecStd = is_array($std->fornec) ? (object) $std->fornec : $std->fornec;

            if (isset($fornecStd->email)) {
                unset($fornecStd->email);
            }
            $this->dom->appChild(
                $documentos,
                $this->tagDest($fornecStd, 'fornec'),
                'Grupo de informações de documento não fiscal'
            );
        }

        $this->dom->addChild(
            $documentos,
            "dtEmiDoc",
            $std->dtEmiDoc,
            true,
            $identificador . "Data da emissão do documento dedutível. Ano, mês e dia (AAAA-MM-DD)"
        );
        
        $this->dom->addChild(
            $documentos,
            "dtCompDoc",
            $std->dtCompDoc,
            true,
            $identificador . "Data da competência do documento dedutível. Ano, mês e dia (AAAA-MM-DD)"
        );

        $this->dom->addChild(
            $documentos,
            "tpReeRepRes",
            $std->tpReeRepRes,
            true,
            $identificador . "Tipo de valor incluído neste documento"
        );

        $this->dom->addChild(
            $documentos,
            "xTpReeRepRes",
            $std->xTpReeRepRes,
            false,
            $identificador . "Descrição do reembolso ou ressarcimento quando a opção é 99"
        );

        $this->dom->addChild(
            $documentos,
            "vlrReeRepRes",
            $this->conditionalNumberFormatting($std->vlrReeRepRes),
            true,
            $identificador . "Valor monetário (total ou parcial, conforme documento informado) utilizado para não inclusão na base de cálculo do ISS e do IBS e da CBS da NFS-e que está sendo emitida (R$)"
        );

        $this->gReeRepRes->addappendChild($documentos);

        if (!empty($this->valores)) {
            $node = $this->valores->getElementsByTagName("trib")->item(0);
            $currentgReeRepRes = $this->valores->getElementsByTagName("gReeRepRes")->item(0);
            if (isset($currentgReeRepRes)) {
                $this->valores->removeChild($currentgReeRepRes);
            }
            
            $this->valores->insertBefore($this->gReeRepRes, $node);
        }

        return $this->gReeRepRes;
    }

}
/*

 <?php
    $std = (object)[
        'tipoChaveDFe' => '1',
            'xTipoChaveDFe' => 'NFE',
            'chaveDFe' => '12345678901234567890123456789012345678901234',
        ],
        'docFiscalOutro' => (object)[
            'cMunDocFiscal' => '3550308',
            'nDocFiscal' => '1234',
            'xDocFiscal' => 'NF de outro estado',
        ],
        'docOutro' => (object)[
            'nDoc' => '000123',
            'xDoc' => 'Documento não-fiscal',
        ],
        'fornec' => (object)[
            'CNPJ' => '11111111000111',
			'xNome' => 'Fornecedor Terceirizado SA'
        ],
        'dtEmiDoc' => '2025-01-01',
        'dtCompDoc' => '2025-01-01',
        'tpReeRepRes' => '1',
        'vlrReeRepRes' => '1000.00',
        'dFeNacional' => (object)[
    ];
    $trait->tagDocumentos($std); 
 */