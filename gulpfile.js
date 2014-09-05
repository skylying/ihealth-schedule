// ========================================
// Gulp configuration
// ========================================
var configs = [
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
			type: 'normal',    // 'normal', 'single-file'
			dir: __dirname + '/media/com_schedule/css',
			filename: 'screen.css'  // use only in type 'single-file'
		}
	}
];
// ========================================


// ========================================
// Gulp scripts (DO NOT MODIFY IT!!)
// ========================================
var gulp = require('gulp');
var concat = require('gulp-concat');
var less = require('gulp-less');
var liveReload = require('gulp-livereload');
var tasks = ['watch'];
var setup = {
	/**
	 * Setup less gulp task
	 *
	 * @param {string} taskName Task name
	 * @param {object} config   Task configuration
	 */
	less: function(taskName, config) {
		gulp.task(taskName, function() {
			var paths = config.paths.include;

			config.paths.exclude.forEach(function(path) {
				paths.push('!' + path);
			});

			var task = gulp.src(paths);

			task = task.pipe(less());

			if ('single-file' === config.dest.type) {
				task = task.pipe(concat(config.dest.filename));
			}

			task.pipe(gulp.dest(config.dest.dir))
				.pipe(liveReload());
		});
	}
};

configs.forEach(function(config, index) {
	var taskName = config.type + index;

	switch (config.type) {
		case 'less':
			setup.less(taskName, config);
			break;
	}

	tasks.push(taskName);
});

// Rerun the task when a file changes
gulp.task('watch', function() {
	configs.forEach(function(config, index) {
		var paths = [].concat(config.paths.include, config.paths.exclude);
		var taskName = config.type + index;

		gulp.watch(paths, [taskName]);
	});
});

gulp.task('default', tasks);
