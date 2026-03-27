# Generador de Números Aleatorios

## Requisitos
- Docker + docker-compose
- Puerto mapeado: 8082

## Instrucciones

1. Colocar esta carpeta en `./html/noo/` del proyecto que contiene `docker-compose.yml`

2. Ejecutar:
   ```bash
   docker-compose up -d
   ```

3. Abrir en navegador:
   ```
   http://localhost:8082/noo/
   ```

4. PHP 7.4 es el target; la app evita sintaxis de PHP 8+

## Notas
- No se requiere Composer; todas las clases se incluyen con `require_once`
- El formulario genera N números aleatorios dentro de un rango
- Implementa el patrón PRG (Post/Redirect/Get) para evitar reenvíos accidentales
