<?php

/************************************************************/
/*															*/
/*	Ядро системы управления Asterix	CMS						*/
/*		Контроллер системы управления						*/
/*															*/
/*	Версия ядра 2.0.b5										*/
/*	Версия скрипта 1.00										*/
/*															*/
/*	Copyright (c) 2009  Мишин Олег							*/
/*	Разработчик: Мишин Олег									*/
/*	Email: dekmabot@gmail.com								*/
/*	WWW: http://mishinoleg.ru								*/
/*	Создан: 10 февраля 2009	года							*/
/*	Модифицирован: 28 октября 2009 года						*/
/*															*/
/************************************************************/

require('default_controller.php');

class controller_admin extends default_controller{

	//Доступные действия
	public $actions = array(
		'view' => 'Предварительный просмотр записи',
		'addRecord' => 'Добавление записи',
		'editRecord' => 'Редактирование записи',
		'delete' => 'Удаление записи',
		'moveup' => 'Переместить на позицию выше',
		'movedown' => 'Переместить на позицию ниже',
		'move' => 'Переместить по дереву',
		'settings' => 'Настройки сайта',
	);

	//Группы полей
	public $groups = array(
		'main' => array(
			'pos' =>	10,
			'title' => 'Основные',
			'comment' => 'Вверху ↑ видны закладки, в них спрятаны поля для закачки картинок и другие интересные поля.'
		),
		'media' => array(
			'pos' =>	20,
			'title' => 'Картинки',
			'help' => 'Здесь вы можете разместить необходимые для записи изображения, либо звук/видео, если такое предусмотрено на вашем сайте. <br /><br />
				<strong>Картинки</strong>: при закачке выбранного вами изображения, происходит автоматическое создание всех необходимых уменьшенных копий изображения. Их вы можете посмотреть в закладке "Подробнее" у интересующего поля.<br /><br />
				<strong>Видео</strong>: помните, что объём видео-файлов обычно достаточно велик, и скорость закачки видео на сайт сильно зависит от скорости вашего интернета. Будьте терпиливы и дождитесь окончательной закачки файла.<br /><br />
				<strong>Маски</strong>: картинки можно обрезать по маске, для этого необходимо приложить файл с маской, на которой прозрачным (либо чёрным) цветом обозначена область, которая должна остаться в результирующей картинке.<br /><br />
			'
		),
		'show' => array(
			'pos' =>	30,
			'title' => 'Отображение на сайте',
			'comment' => 'Здесь вы можете настроить отображение записи на сайте, в главном меню, или в других предложенных ниже местах'
		),
		'seo' => array(
			'pos' =>	40,
			'title' => 'SEO',
			'comment' => 'Следующие поля предназначены для поисковой оптимизации записи. Здесь можно настроить отличные от общих правил метатеги и заголовок страницы'
		),
		'system' => array(
			'pos' =>	50,
			'title' => 'Системные',
			'warning' => 'В этом разделе содержатся важные системные настройки, меняйте их осторожно'
		),
		'additional' => array(
			'pos' =>	60,
			'title' => 'Дополнительные',
		),
		'links' => array(
			'pos' =>	70,
			'title' => 'Связи на сайте'
		),
		'access' => array(
			'pos' =>	80,
			'title' => 'Права доступа'
		),
		'templates' => array(
			'pos' =>	90,
			'title' => 'Шаблоны'
		),
		'feedback' => array(
			'pos' =>	100,
			'title' => 'Форма обратной связи',
		),
		'social' => array(
			'pos' =>	110,
			'title' => 'Социальный граф',
		),
		'contacts' => array(
			'pos' =>	120,
			'title' => 'Контакты',
		),
		'config' => array(
			'pos' =>	130,
			'title' => 'Конфигурация',
		)
	);

	public function start(){
		if( model::$ask->method == 'GET' )
			$this->preloadForm( model::$ask->mode[0], model::$ask->rec );
		else
			$this->controlForm( model::$ask->mode[0], model::$ask->rec );
	}
	
	//Форма редактирования записи
	private function preloadForm($action, $record){
		
		//Ресурсы		
		if( !$action ){
			$action = 'tree';
			model::$ask->output_type = $action;
			$record = false;
		}elseif($action == 'addRecord'){
			$this->checkRestBeforeAdd();
			$record = false;
		}elseif($action == 'editRecord'){
			$this->checkRestBeforeEdit();
		}elseif( in_array($action, array('settings')) ){
			model::$ask->module = 'start';
			model::$ask->structure_sid = 'rec';
			model::$ask->output_type = 'content';
		}elseif( in_array($action, array('users', 'css', 'js', 'templates', 'modules')) ){
			model::$ask->module = 'start';
			model::$ask->structure_sid = 'rec';
			if( IsSet(model::$ask->mode[3]) )
				model::$ask->output_type = 'content';
			else
				model::$ask->output_type = 'list';
		}elseif( in_array($action, array('access')) ){
			model::$ask->module = 'start';
			model::$ask->structure_sid = 'rec';
			model::$ask->output_type = 'access';
		}
		
		//Ошибки
		if( !user::is_admin() )
			log::stop('400 Bad Request');
		if( model::$ask->output_type == 404 )
			log::stop('404 Not Found');
		if( !IsSet( $action ) )
			log::stop('501 Not Implemented');

		//Шаблоны
		$current_template_file = 'admin.tpl';
		require(model::$config['path']['core'] . '/classes/templates.php');
		$tmpl = new templater($this->model);

		//Данные
		$action_result['title']					= $this->actions[ $action ] .' '. model::$modules[ model::$ask->module ]->structure[$structure_sid]['title'];
		$action_result['module']				= model::$ask->module;
		$action_result['structure_sid']			= model::$ask->structure_sid;
		$action_result['form_action']			= $action;
		$action_result['content_template_file']	= 'forms/form_'.model::$ask->output_type.'.tpl';
		$action_result['content_template_file'] = $tmpl->correctTemplatePackPath($action_result['content_template_file'], 'admin_templates');
		
		$action_result['groups'] 				= $this->getGroups($action, $record);

		$tmpl->assign('action', 				$action_result);
		$tmpl->assign('ask', 					model::$ask);
		$tmpl->assign('content', 				model::$ask->rec);
		$tmpl->assign('paths', 					model::$config['path']);
		$tmpl->assign('settings', 				model::$settings);
		$tmpl->assign('path_admin_templates', 	model::$config['path']['admin_templates']);
		$tmpl->assign('domain', 				$this->model->extensions['domains']->domain);
		
		//Компилируем
		try {
			$ready_html = $tmpl->fetch('forms/body.tpl', 'admin_templates');
		}catch (Exception $e) {
			log::stop('500 Internal Server Error', 'Ошибка компиляции', $e);
		}

		header('Content-Type: text/html; charset=utf-8');
		header("HTTP/1.0 200 Ok");
		
		echo $ready_html;
		exit();
	}
	
	//Контроллер обработки отправленной формы
	private function controlForm($action, $record){
	
		if( $action == 'addRecord' ){
			$this->checkRestBeforeAdd();
			$url = $this->model->addRecord(model::$ask->module, model::$ask->structure_sid, $this->vars);
			
		}elseif( $action == 'editRecord' ){
			$this->checkRestBeforeEdit();
			model::$modules[ model::$ask->module ]->controlInterface('editRecord', $this->vars);
			
		}elseif( $action == 'move' ){
			model::$modules[ $this->vars['module_sid'] ]->moveTo($this->vars, $this->vars['structure_sid']);
			exit();
			
		}elseif( $action == 'delete' ){
			$record = model::$modules[ $this->vars['module_sid'] ]->getRecordById($this->vars['structure_sid'], $this->vars['record']);
			if( $record )
				$res = model::$modules[ $this->vars['module_sid'] ]->deleteRecord($record, $this->vars['structure_sid']);
			exit();
			
		}elseif( $action == 'settings' ){
			model::$ask->module = 'start';
			model::$ask->structure_sid = 'rec';
			model::$ask->output_type = 'content';
			$url = $this->updateSettings();
			
		}elseif( $action == 'templates' ){
			$file = model::$config['path']['templates'].'/'.str_replace('_tpl', '.tpl', $this->vars['id']);
			file_put_contents($file, $this->vars['html']);
			chmod($file, 0775);
			header('Location: /admin/start.templates.html');
			exit();
			
		}elseif( $action == 'css' ){
			$file = model::$config['path']['styles'].'/'.str_replace('_css', '.css', $this->vars['id']);
			file_put_contents($file, $this->vars['html']);
			chmod($file, 0775);
			header('Location: /admin/start.css.html');
			exit();
			
		}elseif( $action == 'js' ){
			$file = model::$config['path']['javascript'].'/'.str_replace('_js', '.js', $this->vars['id']);
			file_put_contents($file, $this->vars['html']);
			chmod($file, 0775);
			header('Location: /admin/start.js.html');
			exit();
			
		}elseif( $action == 'access' ){
			pr_r($this->vars);
			exit();
			
		}else{
			log::stop('404 Not Found');
		}
			
		if( !user::is_admin() )
			log::stop('400 Bad Request');
		if( model::$ask->output_type == 404 )
			log::stop('404 Not Found');
		if( !IsSet( $this->actions[ model::$ask->mode[0] ]) )
			log::stop('501 Not Implemented');

//		header('Location: '.$url);
		header('Location: /admin.html');
		exit();
	}

	private function checkRestBeforeAdd(){
		if( IsSet( model::$ask->rec['is_link_to_module'] ) )
			if( model::$ask->rec['is_link_to_module'] )
				model::$ask->module = model::$ask->rec['is_link_to_module'];
		if( IsSet( model::$ask->mode[1] ) )
			model::$ask->structure_sid = model::$ask->mode[1];
		if( model::$ask->structure_sid == 'rec' )
			model::$ask->output_type = 'content';
		else
			model::$ask->output_type = 'list';
	}
	private function checkRestBeforeEdit(){
		if( (model::$ask->output_type == 'index') and (model::$ask->module) and (model::$ask->controller == 'admin') ){
			model::$ask->module = 'start';
			model::$ask->structure_sid = 'rec';
			model::$ask->output_type = 'content';
		}
	}
	
	private function getGroups($action, $record) {
		
		//Поля для данных
		if( $action == 'settings' )
			$fields = $this->getSettingsFields();
		elseif( $action == 'tree' )
			$fields = $this->getTree();

		elseif( $action == 'templates' and !IsSet(model::$ask->mode[3]) )
			$fields = $this->getTemplates();
		elseif( $action == 'templates' and model::$ask->mode[3] == 'editRecord' )
			$fields = $this->getOneTemplate( model::$ask->mode[1].'.'.model::$ask->mode[2] );

		elseif( $action == 'css' and !IsSet(model::$ask->mode[3]) )
			$fields = $this->getCSS();
		elseif( $action == 'css' and model::$ask->mode[3] == 'editRecord' )
			$fields = $this->getOneCSS( model::$ask->mode[1].'.'.model::$ask->mode[2] );

		elseif( $action == 'js' and !IsSet(model::$ask->mode[3]) )
			$fields = $this->getJS();
		elseif( $action == 'js' and model::$ask->mode[3] == 'editRecord' )
			$fields = $this->getOneJS( model::$ask->mode[1].'.'.model::$ask->mode[2] );

		elseif( $action == 'access' )
			$fields = $this->getAccess();
			
		else{
			$fields = model::$modules[ model::$ask->module ]->prepareInterface($action, array('id'=>$record['id']), false);
			$fields = $fields['fields'];
//			$fields = $this->getRecordFields(model::$ask->module, model::$ask->structure_sid, $record, false);
		}

		$groups = $this->sortFieldsToGroups( $fields );
		
		//Готово
		return $groups;
	}

	public function getSettingsFields(){
		$fields = $this->model->execSql('select * from `settings`','getall');
		foreach( $fields as $i => $field ){
			if($field['field'])
				$field = array_merge( unserialize( $field['field'] ), $field );
			$field['sid'] = $field['var'];
			UnSet($field['var']);
			if( !$field['type'])
				$field['type'] = 'text';
			if( !$field['sid'])
				$field['sid'] = 'val';
			
			if( !IsSet($field['value']) )
				$field['value'] = model::$types[ $field['type'] ]->getDefaultValue($field);
			if( !is_array( $field['value'] ) )
				$field['value'] = model::$types[ $field['type'] ]->getAdmValueExplode( $field['value'], $field);
			$field['template_file'] = model::$types[ $field['type'] ]->template_file;
			
			$fields[$i] = $field;
		}
		return $fields;
	}

	public function getTree(){
		foreach(model::$modules as $module_sid=>$module){
			if( is_array($module->structure) )
			foreach($module->structure as $structure_sid=>$structure)if( !$structure['hide_in_tree'] ){
				$sortable = (IsSet($structure['fields']['pos']) || ($structure['type'] == 'tree'));
				$recs = $module->getModuleShirtTree(false, $structure_sid, 10);
				
				if( is_array($recs) )				
				foreach($recs as $i=>$rec){
					$fields[ 1000*$module->info['id'] + $i ] = 
						array_merge(
							$rec, 
							array(
								'group' => $module->info['title'], 
								'tree_level' => 1, 
								'sortable' => $sortable
							)
						);
				}
			}
		}
		ksort($fields);
		return $fields;
	}
	
	public function getTemplates(){
		
		//Достаём файлы
		include_once( model::$config['path']['libraries'].'/acmsDirs.php' );
		$files = acmsDirs::get_files(model::$config['path']['templates']);
		$files = acmsDirs::select_ext('tpl', $files);
		asort($files);
		
		//Какие шаблоны должны быть
		$tmpl = array();
		foreach(model::$modules as $module_sid=>$module){
			$title = $module->info['title'].' - главная страница';
			if( $module_sid == 'start' )
				$title = 'Главная страница сайта';
			$tmpl[ $module->info['id']*100+1 ] = array('title'=>$title, 'file'=>$module_sid.'_index.tpl');
			$tmpl[ $module->info['id']*100+2 ] = array('title'=>$module->info['title'].' - страница записи', 'file'=>$module_sid.'_content.tpl');
			if( count($module->structures)>1 )
				$tmpl[ $module->info['id']*100+3 ] = array('title'=>$module->info['title'].' - список записей', 'file'=>$module_sid.'_list.tpl');
		}
		ksort($tmpl);
		
		foreach($tmpl as $i=>$t){
			$t['type'] = 'html';
			$t['value'] = false;
			foreach($files as $j=>$file)
				if(substr_count($file, $t['file'])){
					$t['value'] = file_get_contents($file);
					UnSet( $files[$j] );
				}
			$t['sid'] = $t['file'];
			$t['group'] = 'main';
			$t['url_clear'] = '/start.templates.'.$t['file'];
			
			$tmpl[$i] = $t;			
		}
		
		foreach($files as $file)
			$tmpl[] = array(
				'title' => basename($file),
				'sid' => basename($file),
				'type' => 'html',
				'value' => file_get_contents($file),
				'group' => 'Дополнительные шаблоны',
				'url_clear' => '/start.templates.'.basename($file),
			);
		return $tmpl;
	}
	public function getOneTemplate($file){
		$fields[] = array(
			'sid' => 'id',
			'type' => 'id',
			'value' => $file,
			'group' => 'main',
			'template_file' => model::$types[ 'id' ]->template_file,
		);
		$fields[] = array(
			'sid' => 'html',
			'title' => 'Шаблон '.basename($file),
			'type' => 'html',
			'value' => @file_get_contents(model::$config['path']['templates'].'/'.$file),
			'group' => 'main',
			'template_file' => model::$types[ 'html' ]->template_file,
		);
		return $fields;
	}	
	public function getCSS(){
		//Достаём файлы
		include_once( model::$config['path']['libraries'].'/acmsDirs.php' );
		$files = acmsDirs::get_files(model::$config['path']['styles']);
		$files = acmsDirs::select_ext('css', $files);
		asort($files);

		foreach($files as $file)
			$tmpl[] = array(
				'title' => basename($file),
				'sid' => basename($file),
				'type' => 'html',
				'value' => file_get_contents($file),
				'group' => 'main',
				'url_clear' => '/start.css.'.basename($file),
			);
		
		return $tmpl;
	}
	public function getOneCSS($file){
		$fields[] = array(
			'sid' => 'id',
			'type' => 'id',
			'value' => $file,
			'group' => 'main',
			'template_file' => model::$types[ 'id' ]->template_file,
		);
		$fields[] = array(
			'sid' => 'html',
			'title' => 'Файл стилей '.basename($file),
			'type' => 'html',
			'value' => @file_get_contents(model::$config['path']['styles'].'/'.$file),
			'group' => 'main',
			'template_file' => model::$types[ 'html' ]->template_file,
		);
		
		return $fields;
	}	
	public function getJS(){
		//Достаём файлы
		include_once( model::$config['path']['libraries'].'/acmsDirs.php' );
		$files = acmsDirs::get_files(model::$config['path']['javascript']);
		$files = acmsDirs::select_ext('js', $files);
		asort($files);

		foreach($files as $file)
			$tmpl[] = array(
				'title' => basename($file),
				'sid' => basename($file),
				'type' => 'html',
				'value' => file_get_contents($file),
				'group' => 'main',
				'url_clear' => '/start.js.'.basename($file),
			);
		
		return $tmpl;
	}
	public function getOneJS($file){
		$fields[] = array(
			'sid' => 'id',
			'type' => 'id',
			'value' => $file,
			'group' => 'main',
			'template_file' => model::$types[ 'id' ]->template_file,
		);
		$fields[] = array(
			'sid' => 'html',
			'title' => 'Файл '.basename($file),
			'type' => 'html',
			'value' => @file_get_contents(model::$config['path']['javascript'].'/'.$file),
			'group' => 'main',
			'template_file' => model::$types[ 'html' ]->template_file,
		);
		
		return $fields;
	}	
	public function getAccess(){
		$fields = interfaces::getAllInterfaces();
		return $fields;
	}	
	
	public function sortFieldsToGroups($fields){
		require_once ( model::$config['path']['libraries'].'/tree_sort.php');

		$groups = array();		
		foreach($fields as $field){
			if(!$field['group'])
				$field['group'] = 'main';
			
			if( !IsSet($groups[ $field['group'] ]) ){
				if( IsSet( $this->groups[ $field['group'] ] ) )
					$groups[ $field['group'] ] = $this->groups[ $field['group'] ];
				else
					$groups[ $field['group'] ] = array(
						'pos' => 200 + (count($groups)+1)*10,
						'title' => $field['group'],
					);
			}
				
			$groups[ $field['group'] ]['fields'][] = $field;
		}
		
		//Сортируем закладки по порядку
		$groups = prepareTreeSort(
			array(
				'content' => $groups,
				'order' => 'pos',
			)
		);
		
		return $groups;
	}

	//Сохраняем настройки
	private function updateSettings(){
		$sets = $this->model->execSql('select * from `settings` where ' . model::pointDomain() . '', 'getall');
		
		foreach ($sets as $set) {
			if (IsSet($this->vars[$set['var']]) or ($set['type'] == 'check')) {
				$in_str = model::$types[$set['type']]->toSql($set['var'], $this->vars, $set);
				$in_str = str_replace('`' . $set['var'] . '`=', '`value`=', $in_str);
				$sql    = 'update `settings` set ' . $in_str . ' where ' . model::pointDomain() . ' and `var`="' . mysql_real_escape_string($set['var']) . '"';
				$this->model->execSql($sql, 'update');
			}
		}
		header('Location: /admin/start.settings.rec.success.html');
		exit();
	}

}

?>