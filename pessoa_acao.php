<?php

/*
* Código adaptado a partir do código do professor Rodrigo Curvello
* Controlador responsável pela manutenção do cadastro da entidade Pessoa
* @author Wesley R. Bezerra <wesley.bezerra@ifc.edu.br>
* @version 0.1
*/

define("DESTINO", "index.php");
define('ARQUIVO_XML', 'C:\\Users\\Samuel\\Desktop\\pessoa_xml\\dados.xml');

$acao = "";
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $acao = isset($_GET['acao']) ? $_GET['acao'] : "";
        break;
    case 'POST':
        $acao = isset($_POST['acao']) ? $_POST['acao'] : "";
        break;
}

switch ($acao) {
    case 'Salvar':
        salvar();
        break;
    case 'Alterar':
        alterar();
        break;
    case 'excluir':
        excluir();
        break;
}

/*
* Método que converte para xml
*/
function tela2array($array_dados, $xml_dados)
{
    $xml_dados->addChild('id', $array_dados['id']);
    $xml_dados->addChild('nome', $array_dados['nome']);
    $xml_dados->addChild('peso', $array_dados['peso']);
    $xml_dados->addChild('altura', $array_dados['altura']);

    return $xml_dados;
}

// Função para salvar dados em xml
function salvar_xml($dados, $arquivo)
{
    $dados->asXML($arquivo);
}

// Função para ler o xml
function ler_xml($arquivo)
{
    $xml = simplexml_load_file($arquivo);
    return $xml;
}

// Função para carregar dados
function carregar($id)
{
    $xml = ler_xml(ARQUIVO_XML);

    foreach ($xml->children() as $child) {
        if ($child->id == $id) {
            return (array) $child;
        }
    }
}

// Função para alterar dados 
function alterar()
{
    $novo = tela2array($_POST, new SimpleXMLElement('<dados></dados>'));

    $xml = ler_xml(ARQUIVO_XML);

    foreach ($xml->children() as $child) {
        if ($child->id == $novo->id) {
            tela2array($_POST, $child);
        }
    }
    salvar_xml($xml, ARQUIVO_XML);
    header("location:" . DESTINO);
}

// Função para excluir dados
function excluir()
{
    $id = isset($_GET['id']) ? $_GET['id'] : "";
    $xml = ler_xml(ARQUIVO_XML);

    $novo = new SimpleXMLElement('<dados></dados>');

    foreach ($xml->children() as $child) {
        if ($child->id != $id) {
            tela2array((array) $child, $novo);
        }
    }
    salvar_xml($novo, ARQUIVO_XML);

    header("location:" . DESTINO);
}

function salvar()
{
    $pessoa = tela2array($_POST, new SimpleXMLElement('<?xml version="1.0"?><dados></dados>'));

    if (!file_exists(ARQUIVO_XML)) {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><dados></dados>');
    } else {
        $xml = ler_xml(ARQUIVO_XML);
    }

    tela2array($_POST, $xml->addChild('pessoa'));

    salvar_xml($xml, ARQUIVO_XML);

    header("location:" . DESTINO);
}

function array2xml($data, $xml_element) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            // Se o valor for um array, chamamos recursivamente a função
            $sub_element = $xml_element->addChild($key);
            array2xml($value, $sub_element);
        } else {
            // Se for um valor simples, adicionamos como um elemento ao XML
            $xml_element->addChild($key, $value);
        }
    }
}

?>
