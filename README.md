# Что это?
ZakupTeam позволяет посредникам удобно: искать тендеры 44/223 ФЗ, работать с ними, вычислять ожидаемую прибыль

Возможности
- Слова для исключения в поиске, например что бы не показывать тендеры связанные с медициной
- Более точное определение окончания подачи заявок чем на торговых площадках (с учетом переноса)
- Подписка, позволяющая задать правила поиска и отбора, что бы автоматически собирать интересные тендеры
- Добавление заметок к тендеру, с возможностью вставки таблиц, форматированного текста
- Умная таблица расчета для каждого тендера
- Календарь событий что бы не пропустить торги

# Как установить
Подойдет любой хостинг с PHP 5/7/8, для включения подписки нужна поддержка Cron
1. Скачайте и распакуйте файлы в корневую директорию
2. Отредактируйте `php/config.php`
3. Добавить правило `php php/collect_notify.php cron` в планировщике

![alt Предпологаемый рабочий процесс](https://i.ibb.co/nMcj4R0/process.png)
