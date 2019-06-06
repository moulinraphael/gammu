CREATE TABLE `services` (
  `code` varchar(255) NOT NULL,
  `only` text NOT NULL,
  `default` tinyint(1) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
