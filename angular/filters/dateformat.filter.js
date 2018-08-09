export function DateformatFilter(){
    'ngInject';

    return function( input ){
		if (input == null) { return ""; }
            var formattedDate = (new Date(input));
            return formattedDate;
		
    }
}
