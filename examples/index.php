<?php 
	session_start();

	include "../lib/debug.php";

	$_SESSION["NOME"] = "Lucas Saulo Daniel";
	Debug::start();

	Debug::custom("teste");
	Debug::custom("teste2");
	Debug::custom("teste3");
	Debug::custom("Teste", array("usuario" => "Geovane", "parceiro" => "Gean"));
	Debug::custom("Teste 2", array("id" => 1, "name" => "Gean", "musica" => "Dois corações e uma história"));
	Debug::custom(array("usuario" => "Geovane", "parceiro" => "Gean"));

	Debug::logInfo("Dois corações e uma história");

	Debug::db("SELECT * FROM usuariofrom U inner  join tipo T ON T.id = U.id");
	Debug::db("SELECT tipo as tipo, (select * from teste) as teste FROM tipo_usuario");
	Debug::db("
		SELECT 	id,
				nome,
				tipo,
				endereco AS logradouro
		FROM 	usuario U
			INNER JOIN tipo T ON
				T.id = U.idTipo
			LEFT OUTER JOIN endereco E ON
				E.id = U.id
		WHERE 	id = 32 AND
				tipo IN (SELECT id FROM TIPO WHERE id = 3) OR
				id NOT EXISTS 					
	");

	Debug::logInfo("Executando a query de usuário");

	echo $a;
	echo $b;
	echo password_hash('asdfasdf', PASSWORD_BCRYPT, ['salt' => '12345678901234567890123']);
?>
<html>
	<head>
		<title>Debug::Teste</title>
	</head>
	<body>

		<p>
			Lorem Ipsum é simplesmente uma simulação de texto da indústria tipográfica e de impressos, e vem sendo utilizado desde o século XVI, quando um impressor desconhecido pegou uma bandeja de tipos e os embaralhou para fazer um livro de modelos de tipos. Lorem Ipsum sobreviveu não só a cinco séculos, como também ao salto para a editoração eletrônica, permanecendo essencialmente inalterado. Se popularizou na década de 60, quando a Letraset lançou decalques contendo passagens de Lorem Ipsum, e mais recentemente quando passou a ser integrado a softwares de editoração eletrônica como Aldus PageMaker.
		</p>	

		<?php print Debug::show();?>	
	</body>
</html>
