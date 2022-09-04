--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `_posts` (
`id` int(8) NOT NULL,
  `post_title` varchar(255) NOT NULL,
  `description` text NOT NULL,
   `image` varchar(255) NOT NULL,
  `post_at` date DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--
