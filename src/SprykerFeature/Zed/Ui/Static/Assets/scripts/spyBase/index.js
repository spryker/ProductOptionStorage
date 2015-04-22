'use strict';

/**
 * @ngdoc module
 * @name spyBase
 */
require('Ui').ng
	.module('spyBase', [])
	.config([
		'$httpProvider',

		function($httpProvider) {
			$httpProvider.interceptors.push('redirectInterceptor');
			$httpProvider.interceptors.push('errorInterceptor');
		}
	]);



require('./config/interpolateProvider');
require('./config/redirectInterceptor');
require('./config/errorInterceptor');

require('./service/JSONModelDenormalizeService');
require('./service/ArrayModelTransformService');

require('./controller/ComController');
require('./controller/TemplateActionController');
require('./controller/EventDirectiveController');

require('./directive/spyApp');
require('./directive/spyListen');