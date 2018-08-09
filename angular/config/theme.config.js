export function ThemeConfig($mdThemingProvider) {
	'ngInject';
	/* For more info, visit https://material.angularjs.org/#/Theming/01_introduction */
	var customWarn = {
		'50': '#FBCA9A',
		'100': '#FABE81',
		'200': '#F9B169',
		'300': '#F8A450',
		'400': '#F79838',
		'500': '#F68B1F',
		'600': '#F27E0A',
		'700': '#D97109',
		'800': '#C16508',
		'900': '#A85807',
		'A100': '#FCD7B2',
		'A200': '#FDE4CB',
		'A400': '#FEF1E3',
		'A700': '#904B06'
	};
	$mdThemingProvider
		.definePalette('customWarn',
		customWarn);

	$mdThemingProvider.theme('default')
		.primaryPalette('grey', {
            default: '600'
        })
		.accentPalette('customWarn')
		.warnPalette('customWarn', {
			default: '500'
		});

    $mdThemingProvider.theme('warn');
}
