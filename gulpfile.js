require('gulp-sms')(require('gulp'), [
	{
		type: 'less',
		paths: {
			include: [
				__dirname + '/media/com_schedule/css/*.less'
			],
			exclude: [
				__dirname + '/media/com_schedule/css/_*.less'
			]
		},
		dest: {
			type: 'normal',
			dir: __dirname + '/media/com_schedule/css'
		}
	}
]);
