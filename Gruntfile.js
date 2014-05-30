module.exports = function(grunt)
{
	grunt.initConfig(
	{
		phpunit:
		{
			classes:
			{
				dir: 'tests'
			},
			options:
			{
				// logTap: 'storage/logs/tests.log',
				colors: true
			}
		},
		watch:
		{
			test:
			{
				files: ['src/**', 'tests/**'],
				tasks: ['phpunit']
			},
			// css:
			// {
			// 	files: ['src/resources/*/*/styles/scss/*', '/src/*'],
			// 	tasks: ['sass'],
			// 	options: {
			// 		livereload: true,
			// 	}
			// }
			// livescript:
			// {
				
			// }
		}
	});

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-phpunit');
};
