<?php

namespace NFePHP\NFSe\Models\Prodam;

/**
 * Classe para a renderização dos RPS em XML para a Cidade de São Paulo
 * conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\RenderRPS
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Strings;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagAtvEvento;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagChaveRPS;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagCPFCNPJNIF;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagDest;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagEnd;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagEnderecoExterior;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagEnderecoTomador;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagIBSCBS;
use NFePHP\NFSe\Models\Prodam\Traits\TraitTagValores;
use stdClass;

class RenderRPS
{
    use TraitTagAtvEvento;
    use TraitTagChaveRPS;
    use TraitTagCPFCNPJNIF;
    use TraitTagDest;
    use TraitTagEnd;
    use TraitTagEnderecoExterior;
    use TraitTagEnderecoTomador;
    use TraitTagIBSCBS;
    use TraitTagValores;

    protected Dom $dom;
    protected Certificate $certificate;
    protected int $algorithm;

    public function __construct(Certificate $certificate, $algorithm = OPENSSL_ALGO_SHA1)
    {
        $this->certificate = $certificate;
        $this->algorithm = $algorithm;
    }

    public function getXml(mixed $data): string
    {
        $xml = '';
        if (is_object($data)) {
            return $this->render($data);
        } elseif (is_array($data)) {
            foreach ($data as $rps) {
                $xml .= $this->render($rps);
            }
        }
        return $xml;
    }

    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @return string
     */
    private function render(Rps $rps)
    {
        $this->dom = new Dom('1.0', 'utf-8');
        $root = $this->dom->createElement('RPS');
        $xmlnsAttribute = $this->dom->createAttribute('xmlns');
        $xmlnsAttribute->value = '';
        $root->appendChild($xmlnsAttribute);
        //tag Assinatura
        $this->dom->addChild(
            $root,
            'Assinatura',
            $this->signstr($rps),
            true,
            'Tag assinatura do RPS vazia',
        );
        //tag ChaveRPS
        $chaveRps = $this->tagChaveRPS((object) $rps->chaveRPS);
        $this->dom->appChild($root, $chaveRps, 'Adicionando tag ChaveRPS');

        //outras tags
        $this->dom->addChild(
            $root,
            'TipoRPS',
            $rps->tipoRPS,
            true,
            'Tipo de RPS',
        );
        $this->dom->addChild(
            $root,
            'DataEmissao',
            $rps->dataEmissao,
            true,
            'Data de emissão',
        );
        $this->dom->addChild(
            $root,
            'StatusRPS',
            $rps->statusRPS,
            true,
            'Status do RPS'
        );
        $this->dom->addChild(
            $root,
            'TributacaoRPS',
            $rps->tributacaoRPS,
            true,
            'Tributação do RPS',
        );
        $this->dom->addChild(
            $root,
            'ValorDeducoes',
            $rps->valorDeducoes,
            true,
            'Valor das Deduções',
        );
        $this->dom->addChild(
            $root,
            'ValorPIS',
            $rps->valorPIS,
            true,
            'Valor do PIS'
        );
        $this->dom->addChild(
            $root,
            'ValorCOFINS',
            $rps->valorCOFINS,
            true,
            'Valor do COFINS'
        );
        $this->dom->addChild(
            $root,
            'ValorINSS',
            $rps->valorINSS,
            true,
            'Valor do INSS'
        );
        $this->dom->addChild(
            $root,
            'ValorIR',
            $rps->valorIR,
            true,
            'Valor do IR',
        );
        $this->dom->addChild(
            $root,
            'ValorCSLL',
            $rps->valorCSLL,
            true,
            'Valor do CSLL'
        );
        $this->dom->addChild(
            $root,
            'CodigoServico',
            $rps->codigoServico,
            true,
            'Código do serviço'
        );
        $this->dom->addChild(
            $root,
            'AliquotaServicos',
            $rps->aliquotaServicos,
            true,
            'Aliquota do serviço'
        );
        $this->dom->addChild(
            $root,
            'ISSRetido',
            $rps->issRetido ? 'true' : 'false',
            true,
            'ISS Retido'
        );
        //tag CPFCNPJTomador
        if ($rps->cpfCnpjTomador !== null) {
            $tomador = $this->tagCPFCNPJNIF((object) $rps->cpfCnpjTomador, 'CPFCNPJTomador');
            $this->dom->appChild($root, $tomador, 'Adicionando tag CPFCNPJTomador');
        }
        $this->dom->addChild(
            $root,
            'InscricaoMunicipalTomador',
            $rps->inscricaoMunicipalTomador,
            false,
            'Inscrição Municipal do Tomador. ATENÇÃO: Este campo só deverá ser preenchido para tomadores estabelecidos no município de São Paulo (CCM)'
        );
        $this->dom->addChild(
            $root,
            'InscricaoEstadualTomador',
            $rps->inscricaoEstadualTomador,
            false,
            'Inscrição estadual do tomador'
        );
        $this->dom->addChild(
            $root,
            'RazaoSocialTomador',
            $rps->razaoSocialTomador,
            true,
            'Razão Social do tomador'
        );

        //tag EnderecoTomador
        if ($rps->enderecoTomador !== null) {
            $endtomador = $this->tagEnderecoTomador((object) $rps->enderecoTomador);
            $this->dom->addChild(
                $root,
                'EnderecoTomador',
                $endtomador,
                false, // Os campos do endereço são obrigatórios apenas para tomadores pessoa jurídica (CNPJ informado)
                'Endereço do tomador'
            );
        }
        
        $this->dom->addChild(
            $root,
            'EmailTomador',
            $rps->emailTomador,
            false,
            'Email do tomador'
        );
        //tag intermediario
        //se existir incluir dados do intermediário
        if ($rps->cpfCnpjIntermediario !== null) {
            $intermediario = $this->tagCPFCNPJNIF((object) $rps->cpfCnpjTomador, 'CPFCNPJIntermediario');
            $this->dom->appChild($root, $intermediario, 'Adicionando tag CPFCNPJIntermediario');

            $this->dom->addChild(
                $root,
                'InscricaoMunicipalIntermediario',
                $rps->inscricaoMunicipalIntermediario,
                false,
                'IM do intermediario',
            );
            $this->dom->addChild(
                $root,
                'ISSRetidoIntermediario',
                $rps->issRetidoIntermediario ? 'true' : 'false',
                false,
                'Retenção do ISS pelo intermediário de serviço',
            );
            $this->dom->addChild(
                $root,
                'EmailIntermediario',
                $rps->emailIntermediario,
                false,
                'Email do intermediario',
            );
        }
        $this->dom->addChild(
            $root,
            'Discriminacao',
            Strings::replaceSpecialsChars(trim($rps->discriminacao)),
            true,
            'Discriminação do serviço'
        );
	    $this->dom->addChild(
            $root,
            'ValorCargaTributaria',
            $this->conditionalNumberFormatting($rps->valorCargaTributaria),
            false,
            'Valor da carga tributária total em R$.'
        );
        $this->dom->addChild(
            $root,
            'PercentualCargaTributaria',
            $this->conditionalNumberFormatting($rps->percentualCargaTributaria, 4),
            false,
            'Valor percentual da carga tributária'
        );
        $this->dom->addChild(
            $root,
            'FonteCargaTributaria',
            substr(Strings::replaceSpecialsChars($rps->fonteCargaTributaria), 0, 10),
            false,
            'Fonte de informação da carga tributária'
        );

        $this->dom->addChild(
            $root,
            'CodigoCEI',
            $rps->codigoCEI,
            false,
            'Código do CEI - Cadastro específico do INSS'
        );
        $this->dom->addChild(
            $root,
            'MatriculaObra',
            $rps->matriculaObra,
            false,
            'Código que representa a matrícula da obra no sistema de cadastro de obras'
        );
        $this->dom->addChild(
            $root,
            'MunicipioPrestacao',
            $rps->municipioPrestacao,
            false,
            'Código da cidade do município da prestação do serviço'
        );
        $this->dom->addChild(
            $root,
            'NumeroEncapsulamento',
            $rps->numeroEncapsulamento,
            false,
            'Código que representa o número do encapsulamento da obra'
        );


        $this->dom->addChild(
            $root,
            'ValorTotalRecebido',
            $this->conditionalNumberFormatting($rps->valorTotalRecebido),
            true,
            'Valor do total recebido'
        );
        if (isset($rps->valorInicialCobrado)){
            $this->dom->addChild(
                $root,
                'ValorInicialCobrado',
                $this->conditionalNumberFormatting($rps->valorInicialCobrado),
                true,
                'Valor inicial cobrado pela prestação do serviço, antes de tributos, multa e juros'
            );
        } else {
            $this->dom->addChild(
                $root,
                'ValorFinalCobrado',
                $this->conditionalNumberFormatting($rps->valorFinalCobrado),
                true,
                'Valor final cobrado pela prestação do serviço, incluindo todos os tributos'
            );
        }
        $this->dom->addChild(
            $root,
            'ValorMulta',
            $this->conditionalNumberFormatting($rps->valorMulta),
            false,
            'Valor da multa'
        );
        $this->dom->addChild(
            $root,
            'ValorJuros',
            $this->conditionalNumberFormatting($rps->valorJuros),
            false,
            'Valor dos juros'
        );
        $this->dom->addChild(
            $root,
            'ValorIPI',
            $this->conditionalNumberFormatting($rps->valorIPI),
            false,
            'Valor de IPI'
        );

        $this->dom->addChild(
            $root,
            'ExigibilidadeSuspensa',
            (int) $rps->exigibilidadeSuspensa,
            false,
            'Indica se é uma emissão com exigibilidade suspensa'
        );
        $this->dom->addChild(
            $root,
            'PagamentoParceladoAntecipado',
            (int) $rps->pagamentoParceladoAntecipado,
            false,
            'Indica de nota fiscal de pagamento parcelado antecipado (realizado antes do fornecimento)'
        );

        $this->dom->addChild(
            $root,
            'NCM',
            Strings::onlyNumbers($rps->NCM),
            false,
            'Número NCM (Nomenclatura Comum do Mercosul)'
        );
        $this->dom->addChild(
            $root,
            'NBS',
            Strings::onlyNumbers($rps->NBS),
            false,
            'Número NBS (Nomenclatura Brasileira de Serviços)'
        );

        //tag atvEvento
        if ($rps->atvEvento !== null) {
            $atvEvento = $this->tagAtvEvento((object) $rps->atvEvento);
            $this->dom->addChild(
                $root,
                'atvEvento',
                $atvEvento,
                false,
                'Informações dos Tipos de evento'
            );
        }

        // gpPrestacao (escolha entre cLocPrestacao e cPaisPrestacao)

        if (!empty($rps->cPaisPrestacao)) {
            $this->dom->addChild(
                $root,
                'cPaisPrestacao',
                Strings::onlyNumbers($rps->cPaisPrestacao),
                true,
                'País de prestação do serviço'
            );
        } else {
            $this->dom->addChild(
                $root,
                'cLocPrestacao',
                Strings::onlyNumbers($rps->cLocPrestacao),
                true,
                'Cidade de prestação do serviço'
            );
        }
        
        // Grupo de Informações do IBS e da CBS
        $IBSCBS = $this->tagIBSCBS((object) $rps->ibsCbs);
        $this->dom->addChild(
            $root,
            'IBSCBS',
            $IBSCBS,
            true,
            'Informações do IBS e da CBS'
        );
        //finaliza
        $this->dom->appendChild($root);
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $this->dom->saveXML());
        return $xml;
    }

    /**
     * Cria o valor da assinatura do RPS
     * @param Rps $rps
     * @return string
     */
    private function signstr(Rps $rps)
    {
        $content = str_pad($rps->prestadorIM, 8, '0', STR_PAD_LEFT);
        $content .= str_pad($rps->serieRPS, 5, ' ', STR_PAD_RIGHT);
        $content .= str_pad($rps->numeroRPS, 12, '0', STR_PAD_LEFT);
        $content .= str_replace("-", "", $rps->dataEmissao);
        $content .= $rps->tributacaoRPS;
        $content .= $rps->statusRPS;
        $content .= $rps->issRetido ? 'S' : 'N';
        $content .= str_pad(
            str_replace(['.', ','], '', number_format($rps->valorServicos, 2)),
            15,
            '0',
            STR_PAD_LEFT
        );
        $content .= str_pad(
            str_replace(['.', ','], '', number_format($rps->valorDeducoes, 2)),
            15,
            '0',
            STR_PAD_LEFT
        );
        $content .= str_pad($rps->codigoServico, 5, '0', STR_PAD_LEFT);
        $content .= $rps->tomadorTipoDoc;
        $content .= str_pad($rps->tomadorCNPJCPF, 14, '0', STR_PAD_LEFT);
        $content .= $rps->intermediarioTipoDoc;
        $content .= str_pad($rps->intermediarioCNPJCPF, 14, '0', STR_PAD_LEFT);
        $content .= $rps->issRetidoIntermediario ? 'S' : 'N';
        //$contentBytes = $this->getBytes($content);
        $signature = base64_encode($this->certificate->sign($content, $this->algorithm));
        return $signature;
    }

    /**
     * Includes missing or unsupported properties in stdClass
     * Convert all properties of object in lower case
     * Replace all unsuported chars from data
     * @param stdClass $std
     * @param string[] $possible
     * @return stdClass
     */
    protected function equilizeParameters(stdClass $std, array $possible): stdClass
    {
        $ppl = array_map('strtolower', $possible);
        $std = $this->propertiesToLower($std);
        $equalized = Strings::equilizeParameters(
            $std,
            $ppl,
            false
        );
        return $this->propertiesToBack($equalized, $possible);
    }

    /**
     * Change properties names of object to lower case
     * @param stdClass $data
     * @return stdClass
     */
    protected function propertiesToLower(stdClass $data): stdClass
    {
        $properties = get_object_vars($data);
        $clone = new stdClass();
        foreach ($properties as $key => $value) {
            if ($value instanceof stdClass) {
                $value = $this->propertiesToLower($value);
            }
            $nk = trim(strtolower($key));
            $clone->{$nk} = $value;
        }
        return $clone;
    }

    /**
     * Return properties do original name
     * @param stdClass $data
     * @param array $possible
     * @return stdClass
     */
    protected function propertiesToBack(stdClass $data, array $possible): stdClass
    {
        $new = new stdClass();
        $properties = get_object_vars($data);
        foreach ($properties as $key => $value) {
            foreach ($possible as $p) {
                if (strtolower($p) === $key) {
                    $new->$p = $value;
                    break;
                }
            }
        }
        return $new;
    }

    /**
     * Formatação numerica condicional
     * @param string|float|int|null $value
     * @param int $decimal
     * @return string|null
     */
    protected function conditionalNumberFormatting($value = null, int $decimal = 2): ?string
    {
        if (is_numeric($value)) {
            return number_format($value, $decimal, '.', '');
        }
        return null;
    }
}
