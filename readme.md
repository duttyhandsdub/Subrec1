## Outputing CSS in development

After cloning project run to install dependencies

> npm install

Run the required tasks on the command line 'e.g. gulp css-process-dev'

- less-process (process less to css)
- css-compress (compress css file)
- css-prefix (autoprefix css file)
- css-sourcemap (addition of source map inline to css file to help debugging)
- css-process-prod (for production runs following [less-process,css-compress,css-prefix])
- css-process-dev (for development runs following [less-process,css-compress,css-prefix,css-sourcemap])
- watch-dev (adds watcher to the main less file and runs css-process-dev task)
- watch-prod (adds watcher to the main less file and runs css-process-prod task)