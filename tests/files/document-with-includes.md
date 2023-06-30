# Document with includes

Doc viewer files can be processed to include external files
anywhere. For example to load sample code from files instead
of maintaining them in the markdown.

{include-file: includes/test-php-highlight.php}

If a file does not exist, the command is replaced by an
error message.

{include-file: unknown-file.txt}

Same thing for trying to include a folder:

{include-file: includes/}

Or files that are too big:


