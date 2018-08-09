import {RoutesConfig} from './config/routes.config';
import {LoadingBarConfig} from './config/loading_bar.config';
import {ThemeConfig} from './config/theme.config';
import {SatellizerConfig} from './config/satellizer.config';

angular.module('app.config')
    .config(RoutesConfig)
	.config(LoadingBarConfig)
	.config(ThemeConfig)
	.config(SatellizerConfig)
	.config((indexeddbProvider) => {
		indexeddbProvider.setDbName('rcm'); // your database name
		indexeddbProvider.setDbVersion(1); // your database version
		var tables = [{
			name: 'order',
			fields: []
		}];
		indexeddbProvider.setDbTables(tables);
	}).config(($provide) => {
		$provide.constant('Offline', window.Offline);
	});

