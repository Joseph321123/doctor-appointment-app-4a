# Email, PDF y reportes diarios (Fase 2)

## Gmail (correo real con contraseña de aplicaciones)

1. En la cuenta de Google: **Seguridad → Verificación en 2 pasos** (activada) → **Contraseñas de aplicaciones**.
2. Genera una contraseña de 16 caracteres y úsala en `MAIL_PASSWORD` **sin espacios**.
3. En `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu@gmail.com
MAIL_PASSWORD=xxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS="tu@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

`MAIL_FROM_ADDRESS` debe coincidir con `MAIL_USERNAME` para evitar rechazos por Gmail.

Si falla la conexión TLS, prueba `MAIL_SCHEME=tls` en `.env`.

---

## Configuración Mailtrap

1. Crea un inbox en [Mailtrap](https://mailtrap.io) y copia **Username** y **Password** al archivo `.env` (no los subas a GitHub).

2. **Importante:** si `MAIL_MAILER=log` (valor por defecto en Laravel), **no se envía nada a Mailtrap**: los mensajes solo aparecen en `storage/logs/laravel.log`. Debes usar **`MAIL_MAILER=smtp`**.

3. Tras editar `.env`, ejecuta:
   ```bash
   php artisan config:clear
   ```

4. Ejemplo de variables:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_FROM_ADDRESS="noreply@tu-dominio.test"
MAIL_FROM_NAME="${APP_NAME}"
```

Si la conexión SMTP falla, prueba añadir `MAIL_SCHEME=tls` (según tu red/firewall).

### Error 550 "Too many emails per second" (Mailtrap)

El sandbox gratuito limita **correos por segundo**. El código ya **espera 1 segundo** entre envíos a paciente y doctor distintos, y **solo envía un correo** si paciente y doctor comparten el mismo email. Si aún falla, espera unos segundos entre pruebas o revisa el plan en Mailtrap.

## Comprobante PDF al crear una cita

Al guardar una cita desde el panel admin, se envían **hasta 3 correos** con el mismo PDF:

1. Paciente  
2. Médico  
3. Administrador (primer usuario con rol **Administrador** en BD, o `MAIL_NOTIFY_ADMIN` en `.env`)

Entre cada envío hay **10 segundos** de espera (evita límites de Gmail). La petición puede tardar **~20 s** en completarse.

Variables opcionales en `.env` (si están vacías, se usan los emails de los usuarios en BD):

- `MAIL_NOTIFY_PACIENTE`
- `MAIL_NOTIFY_DOCTOR`
- `MAIL_NOTIFY_ADMIN`

Con **Gmail +alias** (`tu+cosa@gmail.com`) recibes los tres en el mismo buzón pero como destinatarios distintos.

## Reportes diarios (Task Scheduling)

- Comando: `php artisan appointments:send-daily-reports`
- **Pruebas:** en `routes/console.php` está programado cada **minuto** (`everyMinute()`).
- **Producción:** cambia a `Schedule::command('appointments:send-daily-reports')->dailyAt('08:00');`

En servidor Linux, añade al crontab del usuario web:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Para probar sin esperar al cron:

```bash
php artisan appointments:send-daily-reports
```

Los usuarios con rol **Administrador** reciben la lista de citas del día; cada **doctor** con citas ese día recibe su propia lista.

## Usuarios demo (seeders)

Tras `php artisan migrate:fresh --seed` o `php artisan db:seed --class=DemoMailUsersSeeder`:

| Rol | Email |
|-----|--------|
| Administrador | joseph.aguilar@tecdesoftware.edu.mx |
| Paciente + Doctor (mismo usuario) | soloinglesupy@gmail.com |

Contraseña por defecto en seeder: `123456`.
