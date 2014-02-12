module.exports = function(config){
    config.set({
    basePath : '../',

    files : [
//      'app/lib/angular/angular.js',
//      'app/lib/angular/angular-*.js',
			'../../web/bundles/dashboard/vendors/angular-1.2.12/angular.js',
			'../../web/bundles/dashboard/vendors/angular-1.2.12/angular-*.js',			
      '../../web/bundles/dashboard/assets/js/**/*.js',
      'test/lib/angular/angular-mocks.js',
      'test/unit/**/*.js'
    ],

    exclude : [
      'app/lib/angular/angular-loader.js',
      'app/lib/angular/*.min.js',
      'app/lib/angular/angular-scenario.js'
    ],

    autoWatch : true,

    frameworks: ['jasmine'],

    browsers : ['Chrome'],

    plugins : [
            'karma-junit-reporter',
            'karma-chrome-launcher',
            'karma-firefox-launcher',
            'karma-jasmine'
            ],

    junitReporter : {
      outputFile: 'test_out/unit.xml',
      suite: 'unit'
    }

})}