# Errors while including files

If an error occurs when loading an include file, the command 
is replaced by an error message.

## Include an unknown file

{include-file: unknown-file.txt}

## Include a folder

{include-file: not-a-file.md}

## Include an unsupported extension

{include-file: disallowed-extension.ext}

## Navigate up in relative path

{include-file: ../includes/document-with-includes.md}
