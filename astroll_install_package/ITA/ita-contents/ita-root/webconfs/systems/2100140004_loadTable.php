<?php
//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
//////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//   ・AnsibleTowerのファイル管理機能
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
	global $g;

	$arrayWebSetting = array();
	$arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSTWRH-MNU-7300101"); // MessageID_SecoundSuffix：01


	/* 履歴管理用のカラムを配列に格納 */
	$tmpAry = array
	  (
		 'TT_SYS_01_JNL_SEQ_ID'           => 'JOURNAL_SEQ_NO'
		,'TT_SYS_02_JNL_TIME_ID'          => 'JOURNAL_REG_DATETIME'
		,'TT_SYS_03_JNL_CLASS_ID'         => 'JOURNAL_ACTION_CLASS'
		,'TT_SYS_04_NOTE_ID'              => 'NOTE'
		,'TT_SYS_04_DISUSE_FLAG_ID'       => 'DISUSE_FLAG'
		,'TT_SYS_05_LUP_TIME_ID'          => 'LAST_UPDATE_TIMESTAMP'
		,'TT_SYS_06_LUP_USER_ID'          => 'LAST_UPDATE_USER'
		,'TT_SYS_NDB_ROW_EDIT_BY_FILE_ID' => 'ROW_EDIT_BY_FILE'
		,'TT_SYS_NDB_UPDATE_ID'           => 'WEB_BUTTON_UPDATE'
		,'TT_SYS_NDB_LUP_TIME_ID'         => 'UPD_UPDATE_TIMESTAMP'
	  );

	/* 画面と１対１で紐付けるテーブルを指定 */
	$table = new TableControlAgent('B_ANSTWR_CONTENTS_FILE','CONTENTS_FILE_ID', $g['objMTS']->getSomeMessage("ITAANSTWRH-MNU-7390101"), 'B_ANSTWR_CONTENTS_FILE_JNL', $tmpAry);
	$tmpAryColumn = $table->getColumns();
	$tmpAryColumn['CONTENTS_FILE_ID']->setSequenceID('B_ANSTWR_CONTENTS_FILE_RIC');
	$tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANSTWR_CONTENTS_FILE_JSQ');

	unset($tmpAryColumn);

	/* QMファイル名プレフィックス */
	$table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSTWRH-MNU-7360101"));
	/* エクセルのシート名 */
	$table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAANSTWRH-MNU-7340101"));

	/* 検索機能の制御 */
	$table->setGeneObject('AutoSearchStart',true);

	/* ファイル埋込変数名 */
	$objVldt = new TextValidator(1, 128, false, '/^CPF_[_a-zA-Z0-9]+$/', $g['objMTS'] -> getSomeMessage("ITAANSTWRH-MNU-7390102"));
	$objVldt -> setRegexp("/^[^\r\n]*$/s","DTiS_filterDefault");

	$c = new TextColumn('CONTENTS_FILE_VARS_NAME',$g['objMTS']->getSomeMessage("ITAANSTWRH-MNU-7390102"));
	$c -> setDescription($g['objMTS']->getSomeMessage("ITAANSTWRH-MNU-7350102"));//エクセル・ヘッダでの説明
	$c -> setValidator($objVldt);
	$c -> setRequired(true); // 登録/更新時には、入力必須
	$c -> setUnique(true); // UX上の一意キー

	$table -> addColumn($c);


	/* ファイル素材 */
	$c = new FileUploadColumn('CONTENTS_FILE',$g['objMTS']->getSomeMessage("ITAANSTWRH-MNU-7390103"));
	$c -> setDescription($g['objMTS']->getSomeMessage("ITAANSTWRH-MNU-7350103"));//エクセル・ヘッダでの説明
	$c -> setMaxFileSize(20971520);//単位はバイト
	$c -> setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
	$c->setRequired(true);//登録/更新時には、入力必須
	$c -> setFileHideMode(true);

	$table -> addColumn($c);

	/* カラムを確定する */
	$table->fixColumn();
	$table->setGeneObject('webSetting', $arrayWebSetting);

	return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);

unset($tmpFx);