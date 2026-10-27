# Log sintético

`audit.log` simula eventos de auditoría de procesos en formato `clave=valor`.
No es un log real de un compromiso: es un ejercicio de caza con un caso normal
y varios casos anómalos.

Formato:

```
timestamp host=<host> ppid=<pid> ppid_comm=<proceso padre> pid=<pid> comm=<proceso> args="<argumentos>"
```

Pistas de lectura:

- `ppid_comm=apache2|php-fpm|node|python` + `comm=sh|bash|curl|wget|nc` es una
  combinación que conviene investigar.
- `ppid_comm=sshd` + `comm=bash` puede ser una sesión administrativa legítima:
  no alcanza con el proceso, hay que cruzar usuario, horario y origen.
- El log no trae IP de origen ni usuario: anotá esa limitación en el entregable.
