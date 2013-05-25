module.exports = (grunt) ->
  grunt.initConfig
    pkg: grunt.file.readJSON "package.json"

    meta:
      file: 'dagora'
      endpoint: 'package/static'
      banner: """
        /* <%= pkg.name %> v<%= pkg.version %> - <%= grunt.template.today("m/d/yyyy") %>
           <%= pkg.homepage %>
           Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %> - Licensed <%= _.pluck(pkg.license, "type").join(", ") %> */

        """

    source:
      coffee: [
        'monocle/*.coffee',
        'monocle/models/*.coffee',
        'monocle/views/*.coffee',
        'monocle/controllers/*.coffee']

      stylesheets: [
        'stylesheets/tuktuk.theme.dagora.styl',
        'stylesheets/tuktuk.inheritance.styl']

      css_core: [
        'components/tuktuk/tuktuk.css',
        'components/tuktuk/tuktuk.icons.css']

      js_core: [
        'components/jquery/jquery.min.js',
        'components/monocle/monocle.js',
        'components/hope/hope.js',
        'components/tuktuk/tuktuk.js']

    coffee:
      compile: files: '<%= meta.endpoint %>/<%= meta.file %>.debug.js': ['<%= source.coffee %>']

    uglify:
      options: compress: false, banner: "<%= meta.banner %>"
      coffee: files: '<%= meta.endpoint %>/<%= meta.file %>.js': '<%= meta.endpoint %>/<%= meta.file %>.debug.js'
      core: files: '<%= meta.endpoint %>/<%= meta.file %>.core.js': '<%= source.js_core %>'

    stylus:
      stylesheets:
        options: compress: true
        files: '<%= meta.endpoint %>/<%= meta.file %>.css': ['<%= source.stylesheets %>']

    concat:
      css:
        src: ['<%= source.css_core %>'],
        dest: '<%= meta.endpoint %>/<%= meta.file %>.core.css'

    watch:
      coffee:
        files: ["<%= source.coffee %>"]
        tasks: ["coffee"]
      stylesheets:
        files: ["<%= source.stylesheets %>"]
        tasks: ["stylus"]
      core:
        files: ["<%= source.js_core %>", "<%= source.css_core %>"]
        tasks: ["uglify:core", "concat"]


  grunt.loadNpmTasks "grunt-contrib-coffee"
  grunt.loadNpmTasks "grunt-contrib-uglify"
  grunt.loadNpmTasks "grunt-contrib-stylus"
  grunt.loadNpmTasks "grunt-contrib-concat"
  grunt.loadNpmTasks "grunt-contrib-watch"

  grunt.registerTask "default", ["coffee", "uglify", "stylus", "concat"]
