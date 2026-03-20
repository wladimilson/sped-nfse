<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMElement;
use DOMException;
use NFePHP\Common\Strings;

/**
 * @property Dom $dom
 * @property DOMElement $this->IBSCBS
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagIBSCBS
{
    /**
     * Informações declaradas pelo emitente referentes ao IBS e à CBS
     * tag <root>/IBSCBS
     * NOTA: Ajustado para NFS-e v2
     */
    public function tagIBSCBS(stdClass $std): DOMElement
    {
        $possible = [
            'finNFSe',
            'indFinal',
            'cIndOp',
            'tpOper',
            'tpEnteGov',
            'indDest',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag IBSCBS -';
        $this->IBSCBS = $this->dom->createElement('IBSCBS');
        $this->dom->addChild(
            $this->IBSCBS,
            'finNFSe',
            Strings::onlyNumbers($std->finNFSe),
            true,
            $identificador . 'Indicador da finalidade da emissão de NFS-e'
        );
        $this->dom->addChild(
            $this->IBSCBS,
            'indFinal',
            Strings::onlyNumbers($std->indFinal),
            true,
            $identificador . 'Indica operação de uso ou consumo pessoal. (0-Não ou 1-Sim)'
        );
        self::$dom->addChild(
            $this->IBSCBS,
            'cIndOp',
            Strings::onlyNumbers($std->cIndOp),
            true,
            $identificador . 'Código indicador da operação de fornecimento, conforme tabela "código indicador de operação"'
        );
        self::$dom->addChild(
            $this->IBSCBS,
            'tpOper',
            Strings::onlyNumbers($std->tpOper),
            false,
            $identificador . 'Tipo de Operação com Entes Governamentais ou outros serviços sobre bens imóveis'
        );
        self::$dom->addChild(
            $this->IBSCBS,
            'tpEnteGov',
            Strings::onlyNumbers($std->tpEnteGov),
            false,
            $identificador . 'Tipo do ente da compra governamental'
        );
        self::$dom->addChild(
            $this->IBSCBS,
            'indDest',
            Strings::onlyNumbers($std->indDest),
            true,
            $identificador . 'Tipo do ente da compra governamental'
        );
        return $this->IBSCBS;
    }

    /**
     * Grupo de NFS-e referenciadas ao IBS e à CBS
     * tag <root>/IBSCBS/gRefNFSe
     * NOTA: Ajustado para NFS-e v2
     */
    public function tagQRefNFSe(stdClass $std): DOMElement
    {
        $possible = [
            'refNFSe',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag IBSCBS/gRefNFSe -';
        $gRefNFSe = $this->dom->createElement('gRefNFSe');
        if (!is_array($std->refNFSe)) {
            $std->refNFSe = [ $std->refNFSe ];
        }

        foreach ($std->refNFSe as $refNFSe) {
            self::$dom->addChild(
                $gRefNFSe,
                'refNFSe',
                $refNFSe,
                true,
                $identificador . 'Número da NFSe referenciada'
            );
        }

        if (!empty($this->IBSCBS)) {
            $node = $this->IBSCBS->getElementsByTagName("tpOper")->item(0);
            $this->IBSCBS->insertBefore($gRefNFSe, $node);
        } else {
            throw new DOMException("A tag IBSCBS não pode ser nula/vazia");
        }
        
        return $this->IBSCBS;
    }

    /**
     * GDestinatário referente ao IBS e à CBS
     * tag <root>/IBSCBS/dest
     * NOTA: Ajustado para NFS-e v2
     */
    public function tagIBSCBSDest(stdClass $std): DOMElement
    {
        $possible = [
            'refNFSe',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag IBSCBS/gRefNFSe -';
        $gRefNFSe = $this->dom->createElement('gRefNFSe');
        if (is_array($std->refNFSe)) {
            foreach ($std->refNFSe as $refNFSe) {
                self::$dom->addChild(
                    $gRefNFSe,
                    'refNFSe',
                    $refNFSe,
                    true,
                    $identificador . 'Número da NFSe referenciada'
                );
            }
        }

        if (!empty($this->IBSCBS)) {
            $node = $this->IBSCBS->getElementsByTagName("tpOper")->item(0);
            $this->IBSCBS->insertBefore($gRefNFSe, $node);
        } else {
            throw new DOMException("A tag IBSCBS não pode ser nula/vazia");
        }
        
        return $this->IBSCBS;
    }
}