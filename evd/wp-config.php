<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'centrodelidera6');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '123');

/** nome do host do MySQL */
define('DB_HOST', '127.0.0.1');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'U|Yzy0xW)l-u# #reeYg56;ZIR6g3I+fJ2lor6XdtU]oq@9BZXu7J-*_r$xkDAvD');
define('SECURE_AUTH_KEY',  '<$7<`.N[SS(mCNo*yfeaHE%oIr H5CvuYi|T^tiQ^J0*/KB0_1=c9yFYFV`VQKqv');
define('LOGGED_IN_KEY',    '^|!}&<Z!|[^nUnQk6E!-`H!hHM+wO%F*yJY;kyFc eiF4@GOX:~/[S0i+Du>z+Kj');
define('NONCE_KEY',        '^Dkqt6Efizy~iMQ{U6c4j:>`rr3+xCZwMK^Nk3Pg)@2y+D6b)X,4|bKkM+0CiV~f');
define('AUTH_SALT',        'Ir5BT/u!@/YBq38dE^QO8]yW:#gE|$;93b-%Gs&pF4XfIUhvYR?7|3E(4^%LM}|-');
define('SECURE_AUTH_SALT', '5!SO3(0MO ~(E6^X:=Bs8VnwDcV|1D7?l}U#88%(XX1W;E^T:)Xi9OOQGmY(|+F;');
define('LOGGED_IN_SALT',   'j%Z<<_7:&$rI#;.9X3;)PGR+[onHk8a*?8eB&^-yro1<+mtJjW-~&hWTjqg-,tnO');
define('NONCE_SALT',       'X--Mj+cSD|--G82R+_u:5O].n>/zlJyE>%W-=;W6vDQO5 zL#7E;akJj,1^wy}vO');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente ao
 * idioma escolhido deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define('WPLANG', 'pt_BR');

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');
