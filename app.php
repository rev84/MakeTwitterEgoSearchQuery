<?php

require "../RevinLibrary/Revin.php";

/*************************************
 * 設定
 *************************************/

// 検索したいワード
define('SEARCH_WORD', 'Revin');

// リツイートを除外
define('EXCLUDE_RETWEETS', true);

// 禁止ワードおよび除外ユーザを記述したリストのファイル
define('FILE_LIST', './list.txt');

// 結果を出力するファイル
define('FILE_OUTPUT', './output.txt');

/*************************************/

$queryArray = array();
$searchArray = array();

// リツイート除外
if(EXCLUDE_RETWEETS) $queryArray[] = 'exclude:retweets';

// リスト読む
$list = file_get_contents(FILE_LIST);
// 改行統一
$list = Revin::NormalizeLine($list);
// 分ける
$listArray = explode("\n", $list);

foreach($listArray as $var){
	// 空行もしくはコメント行は飛ばす
	if($var == '' || preg_match('@^//@', $var)){
		continue;
	}
	// 除外ユーザ
	elseif(preg_match('/^@(.*)$/', $var, $matches)){
		$word = $matches[1];
		
		if(!empty($word)){
			$queryArray[] = '-from:'.$word;
			$queryArray[] = '-to:'.$word;
			$queryArray[] = '-@'.$word;
		}
	}
	// 禁止ワード
	elseif(preg_match('/^-(.*)$/', $var, $matches)){
		$word = $matches[1];
		
		if(!empty($word)){
			$queryArray[] = '-'.$word;
		}
	}
	// 検索ワード
	else{
		$searchArray[] = $var;
	}
}

// 検索ワード統合
if(count($searchArray) == 1){
	$queryArray[] = $searchArray[0];
}
elseif(count($searchArray) > 1){
	$queryArray[] = '('.implode(' OR ', $searchArray).')';
}

// 書き出し
file_put_contents(FILE_OUTPUT, implode(" ", $queryArray));

?>