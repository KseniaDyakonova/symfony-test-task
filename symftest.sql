--
-- Структура таблицы `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `organizations`
--

INSERT INTO `organizations` (`id`, `name`) VALUES
(1, 'ВПИ (филиал) ВолгГТУ'),
(2, 'ЕГРН'),
(6, 'new organization'),
(7, 'new organization 2'),
(8, 'Новая организация'),
(11, 'МВД');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_org` int(11) NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `phone`, `id_user`, `id_org`, `password`, `is_active`) VALUES
(1, 'Adam', 'Smith', '375673', 1, 1, '$2y$13$DiAmEegJHl8W.jx52hMHp.aorBG97yWXjvu.QjvZKos/JJfdJ05Ii', 1),
(4, 'Hop', 'HopHop', '1123375673', 1, 1, '$2y$13$.NRJJLxNv.2jQsTO72Uxmu9Vh3QFBqzJ32OAIXfqLcY75dZ4Z40Ey', 1),
(12, 'Тест', 'Тестовый', '34232535', 5, 1, '$2y$13$ig/U3BiVKQU85sWJK11/POsC2kxkT3rZBfr5m0sOaX7Qd3y/otz6i', 1),
(16, 'Ксюша', 'Дьяконова', '89047710514', 12, 1, '$2y$13$v4TEUHkVI6zembtAfO5gKu8tXNxesOPfBiGEFMGdGeIwa5ofwuuMW', 1),
(20, 'Ксения', 'Дьяконова', '890477105141111', 12, 6, '$2y$13$xOE56RTxwN1QUsZfPhNCnulJwuF9z5XoLGx9FiXg4udPLWXs1gG8y', 1),
(22, 'Ксения', 'Дьяконова', '89047710514222', 12, 7, '$2y$13$0FsWlZ.zCrpbKSpNc.kDkejip8lQlyXs0JOMHybPL6ubF9657QXgm', 1),
(24, 'Вася', 'Форточкин', '728443', 1, 8, '$2y$13$CrQtSNydSMuJXjIbtF88UetWiv8FBT2BaenV8HEBYgBV4hnlPLG3q', 1),
(26, 'Васисуалий', 'Лоханкин', '2132424', 24, 8, '$2y$13$ZAj.cSwZPjq.TWmj9UkeW.DfKXivgCD5Ob0xK1OkbPp/cs4YagWbO', 1),
(29, 'Проверка', 'Регистрации', '987654321', 26, 11, '$2y$13$EIszNW48GB0nMwaCfgfW9uVb2kzwsjJLKlpS4uATKekhm7IawUkrO', 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1483A5E9444F97DD` (`phone`),
  ADD KEY `IDX_1483A5E932C8A3DE` (`id_org`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_UserOrg` FOREIGN KEY (`id_org`) REFERENCES `organizations` (`id`);
COMMIT;