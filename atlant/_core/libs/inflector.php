<?php

class Inflector {

	var $_plural = array(
		'rules' => array(
			'/(s)tatus$/i' => '\1\2tatuses',
			'/(quiz)$/i' => '\1zes',
			'/^(ox)$/i' => '\1\2en',
			'/([m|l])ouse$/i' => '\1ice',
			'/(matr|vert|ind)(ix|ex)$/i'  => '\1ices',
			'/(x|ch|ss|sh)$/i' => '\1es',
			'/([^aeiouy]|qu)y$/i' => '\1ies',
			'/(hive)$/i' => '\1s',
			'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
			'/sis$/i' => 'ses',
			'/([ti])um$/i' => '\1a',
			'/(p)erson$/i' => '\1eople',
			'/(m)an$/i' => '\1en',
			'/(c)hild$/i' => '\1hildren',
			'/(buffal|tomat)o$/i' => '\1\2oes',
			'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
			'/us$/' => 'uses',
			'/(alias)$/i' => '\1es',
			'/(ax|cris|test)is$/i' => '\1es',
			'/s$/' => 's',
			'/^$/' => '',
			'/$/' => 's',
		),
		'uninflected' => array(
			'.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', 'people'
		),
		'irregular' => array(
			'atlas' => 'atlases',
			'beef' => 'beefs',
			'brother' => 'brothers',
			'child' => 'children',
			'corpus' => 'corpuses',
			'cow' => 'cows',
			'ganglion' => 'ganglions',
			'genie' => 'genies',
			'genus' => 'genera',
			'graffito' => 'graffiti',
			'hoof' => 'hoofs',
			'loaf' => 'loaves',
			'man' => 'men',
			'money' => 'monies',
			'mongoose' => 'mongooses',
			'move' => 'moves',
			'mythos' => 'mythoi',
			'niche' => 'niches',
			'numen' => 'numina',
			'occiput' => 'occiputs',
			'octopus' => 'octopuses',
			'opus' => 'opuses',
			'ox' => 'oxen',
			'penis' => 'penises',
			'person' => 'people',
			'sex' => 'sexes',
			'soliloquy' => 'soliloquies',
			'testis' => 'testes',
			'trilby' => 'trilbys',
			'turf' => 'turfs'
		)
	);

	var $_singular = array(
		'rules' => array(
			'/(s)tatuses$/i' => '\1\2tatus',
			'/^(.*)(menu)s$/i' => '\1\2',
			'/(quiz)zes$/i' => '\\1',
			'/(matr)ices$/i' => '\1ix',
			'/(vert|ind)ices$/i' => '\1ex',
			'/^(ox)en/i' => '\1',
			'/(alias)(es)*$/i' => '\1',
			'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
			'/([ftw]ax)es/' => '\1',
			'/(cris|ax|test)es$/i' => '\1is',
			'/(shoe|slave)s$/i' => '\1',
			'/(o)es$/i' => '\1',
			'/ouses$/' => 'ouse',
			'/uses$/' => 'us',
			'/([m|l])ice$/i' => '\1ouse',
			'/(x|ch|ss|sh)es$/i' => '\1',
			'/(m)ovies$/i' => '\1\2ovie',
			'/(s)eries$/i' => '\1\2eries',
			'/([^aeiouy]|qu)ies$/i' => '\1y',
			'/([lr])ves$/i' => '\1f',
			'/(tive)s$/i' => '\1',
			'/(hive)s$/i' => '\1',
			'/(drive)s$/i' => '\1',
			'/([^fo])ves$/i' => '\1fe',
			'/(^analy)ses$/i' => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
			'/([ti])a$/i' => '\1um',
			'/(p)eople$/i' => '\1\2erson',
			'/(m)en$/i' => '\1an',
			'/(c)hildren$/i' => '\1\2hild',
			'/(n)ews$/i' => '\1\2ews',
			'/^(.*us)$/' => '\\1',
			'/s$/i' => ''
		),
		'uninflected' => array(
			'.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', '.*ss'
	),
		'irregular' => array()
	);

	var $_uninflected = array(
		'Amoyese', 'bison', 'Borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus',
		'carp', 'chassis', 'clippers', 'cod', 'coitus', 'Congoese', 'contretemps', 'corps',
		'debris', 'diabetes', 'djinn', 'eland', 'elk', 'equipment', 'Faroese', 'flounder',
		'Foochowese', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'graffiti',
		'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings',
		'jackanapes', 'Kiplingese', 'Kongoese', 'Lucchese', 'mackerel', 'Maltese', 'media',
		'mews', 'moose', 'mumps', 'Nankingese', 'news', 'nexus', 'Niasese',
		'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'pliers', 'Portuguese',
		'proceedings', 'rabies', 'rice', 'rhinoceros', 'salmon', 'Sarawakese', 'scissors',
		'sea[- ]bass', 'series', 'Shavese', 'shears', 'siemens', 'species', 'swine', 'testes',
		'trousers', 'trout','tuna', 'Vermontese', 'Wenchowese', 'whiting', 'wildebeest',
		'Yengeese', 'goods', 'OrderGoods'
	);


	var $_pluralized = array();

	var $_singularized = array();

	function &getInstance() {
		static $instance = array();

		if (!$instance) {
			$instance[0] =& new Inflector();
		}
		return $instance[0];
	}

	function rules($type, $rules = array()) {
		$_this =& Inflector::getInstance();
		$type = '_'.$type;

		foreach ($rules as $rule => $pattern) {
			if (is_array($pattern)) {
			  $_this->{$type}[$rule] = array_merge($pattern, $_this->{$type}[$rule]);
			  unset($rules[$rule], $_this->{$type}['cache' . ucfirst($rule)], $_this->{$type}['merged'][$rule]);
			}
		}
		$_this->{$type}['rules'] = array_merge($rules, $_this->{$type}['rules']);

	}

	function pluralize($word) {
		$_this =& Inflector::getInstance();

		if (isset($_this->_pluralized[$word])) {
			return $_this->_pluralized[$word];
		}

		if (!isset($_this->_plural['merged']['irregular'])) {
			$_this->_plural['merged']['irregular'] = $_this->_plural['irregular'];
		}

		if (!isset($_this->plural['merged']['uninflected'])) {
			$_this->_plural['merged']['uninflected'] = array_merge($_this->_plural['uninflected'], $_this->_uninflected);
		}

		if (!isset($_this->_plural['cacheUninflected']) || !isset($_this->_plural['cacheIrregular'])) {
			$_this->_plural['cacheUninflected'] = '(?:' . join( '|', $_this->_plural['merged']['uninflected']) . ')';
			$_this->_plural['cacheIrregular'] = '(?:' . join( '|', array_keys($_this->_plural['merged']['irregular'])) . ')';
		}

		if (preg_match('/(.*)\\b(' . $_this->_plural['cacheIrregular'] . ')$/i', $word, $regs)) {
			$_this->_pluralized[$word] = $regs[1] . substr($word, 0, 1) . substr($_this->_plural['merged']['irregular'][strtolower($regs[2])], 1);
			return $_this->_pluralized[$word];
		}

		if (preg_match('/^(' . $_this->_plural['cacheUninflected'] . ')$/i', $word, $regs)) {
			$_this->_pluralized[$word] = $word;
			return $word;
		}

		foreach ($_this->_plural['rules'] as $rule => $replacement) {
			if (preg_match($rule, $word)) {
				$_this->_pluralized[$word] = preg_replace($rule, $replacement, $word);
				return $_this->_pluralized[$word];
			}
		}
	}

	function pluralize_rus($word) {
		$cons = 'цкнгшщзхфвпрлджчсмтб';
		$word = preg_replace('/(['.$cons.'])а$/', '\1и', $word);

		return $word;
	}

	function singularize($word) {
		$_this =& Inflector::getInstance();

		if (isset($_this->_singularized[$word])) {
			return $_this->_singularized[$word];
		}

		if (!isset($_this->_singular['merged']['uninflected'])) {
			$_this->_singular['merged']['uninflected'] = array_merge($_this->_singular['uninflected'], $_this->_uninflected);
		}

		if (!isset($_this->_singular['merged']['irregular'])) {
			$_this->_singular['merged']['irregular'] = array_merge($_this->_singular['irregular'], array_flip($_this->_plural['irregular']));
		}

		if (!isset($_this->_singular['cacheUninflected']) || !isset($_this->_singular['cacheIrregular'])) {
			$_this->_singular['cacheUninflected'] = '(?:' . join( '|', $_this->_singular['merged']['uninflected']) . ')';
			$_this->_singular['cacheIrregular'] = '(?:' . join( '|', array_keys($_this->_singular['merged']['irregular'])) . ')';
		}

		if (preg_match('/(.*)\\b(' . $_this->_singular['cacheIrregular'] . ')$/i', $word, $regs)) {
			$_this->_singularized[$word] = $regs[1] . substr($word, 0, 1) . substr($_this->_singular['merged']['irregular'][strtolower($regs[2])], 1);
			return $_this->_singularized[$word];
		}

		if (preg_match('/^(' . $_this->_singular['cacheUninflected'] . ')$/i', $word, $regs)) {
			$_this->_singularized[$word] = $word;
			return $word;
		}

		foreach ($_this->_singular['rules'] as $rule => $replacement) {
			if (preg_match($rule, $word)) {
				$_this->_singularized[$word] = preg_replace($rule, $replacement, $word);
				return $_this->_singularized[$word];
			}
		}
		$_this->_singularized[$word] = $word;
		return $word;
	}

	function camelize($lowerCaseAndUnderscoredWord) {
		return str_replace(" ", "", ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord)));
	}

	function underscore($camelCasedWord) {
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
	}

	function humanize($lowerCaseAndUnderscoredWord) {
		return ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord));
	}

	function tableize($className) {
		return Inflector::pluralize(Inflector::underscore($className));
	}

	function classify($tableName) {
		return Inflector::camelize(Inflector::singularize($tableName));
	}

	function variable($string) {
		$string = Inflector::camelize(Inflector::underscore($string));
		$replace = strtolower(substr($string, 0, 1));
		return preg_replace('/\\w/', $replace, $string, 1);
	}

	function slug($string, $replacement = '_', $map = array()) {
		if (is_array($replacement)) {
			$map = $replacement;
			$replacement = '_';
		}

		$quotedReplacement = preg_quote($replacement, '/');

		$default = array(
			'/à|á|å|â/' => 'a',
			'/è|é|ê|ẽ|ë/' => 'e',
			'/ì|í|î/' => 'i',
			'/ò|ó|ô|ø/' => 'o',
			'/ù|ú|ů|û/' => 'u',
			'/ç/' => 'c',
			'/ñ/' => 'n',
			'/ä|æ/' => 'ae',
			'/ö/' => 'oe',
			'/ü/' => 'ue',
			'/Ä/' => 'Ae',
			'/Ü/' => 'Ue',
			'/Ö/' => 'Oe',
			'/ß/' => 'ss',
			'/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
			'/\\s+/' => $replacement,
			sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
		);

		$map = array_merge($default, $map);
		return preg_replace(array_keys($map), array_values($map), $string);
	}

	function humanizeModelName($modelName) {

		$name = '';
		if ($modelName{0})
			$name = array_pop(explode('_', Inflector::underscore($modelName)));

		return $name;

	}
}
?>