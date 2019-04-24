-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Апр 24 2019 г., 19:11
-- Версия сервера: 8.0.15
-- Версия PHP: 7.2.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `pitomnik`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Pitomnik_Animal`
--

CREATE TABLE `Pitomnik_Animal` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` int(255) DEFAULT NULL,
  `characterAnimal` varchar(10) DEFAULT NULL,
  `idCell` int(255) DEFAULT NULL,
  `typeAnimal` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `Pitomnik_Cell`
--

CREATE TABLE `Pitomnik_Cell` (
  `id` int(11) NOT NULL,
  `typeCell` varchar(10) DEFAULT NULL,
  `typeAnimal` varchar(10) DEFAULT NULL,
  `cost` float DEFAULT NULL,
  `stata` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `Pitomnik_Chek`
--

CREATE TABLE `Pitomnik_Chek` (
  `id` int(11) NOT NULL,
  `moneyWait` float DEFAULT NULL,
  `flag` int(10) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `money` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Pitomnik_Chek`
--

INSERT INTO `Pitomnik_Chek` (`id`, `moneyWait`, `flag`, `source`, `money`) VALUES
(1, 0, 0, 'TreasuryDepartment', 0),
(2, 0, 0, 'AllMoney', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Pitomnik_Transaction`
--

CREATE TABLE `Pitomnik_Transaction` (
  `id` int(11) NOT NULL,
  `type` int(10) DEFAULT NULL,
  `what` varchar(255) DEFAULT NULL,
  `how` float DEFAULT NULL,
  `for` varchar(10) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Pitomnik_Animal`
--
ALTER TABLE `Pitomnik_Animal`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Pitomnik_Cell`
--
ALTER TABLE `Pitomnik_Cell`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Pitomnik_Chek`
--
ALTER TABLE `Pitomnik_Chek`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Pitomnik_Transaction`
--
ALTER TABLE `Pitomnik_Transaction`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Pitomnik_Animal`
--
ALTER TABLE `Pitomnik_Animal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT для таблицы `Pitomnik_Cell`
--
ALTER TABLE `Pitomnik_Cell`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `Pitomnik_Chek`
--
ALTER TABLE `Pitomnik_Chek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `Pitomnik_Transaction`
--
ALTER TABLE `Pitomnik_Transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
