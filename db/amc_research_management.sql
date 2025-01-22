--
-- Database: `amc_research_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `usage_status` enum('available','in use','maintenance') DEFAULT 'available',
  `availability` tinyint(1) DEFAULT '1',
  `added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `name`, `usage_status`, `availability`, `added_by`) VALUES
(1, 'ewer', 'in use', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `equipmentusage`
--

CREATE TABLE `equipmentusage` (
  `usage_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `usage_start` date NOT NULL,
  `usage_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projectassignments`
--

CREATE TABLE `projectassignments` (
  `assignment_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('Researcher','Research Assistant') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `team_members` text,
  `funding` decimal(10,2) NOT NULL,
  `status` enum('ongoing','completed') DEFAULT 'ongoing',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`id`, `project_id`, `user_id`) VALUES
(1, 6, 4);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `report_type` enum('research_progress','project_funding','equipment_usage') NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `researchers`
--

CREATE TABLE `researchers` (
  `researcher_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` varchar(255) NOT NULL,
  `expertise` varchar(255) NOT NULL,
  `assigned_projects` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `researchers`
--

INSERT INTO `researchers` (`researcher_id`, `name`, `contact_info`, `expertise`, `assigned_projects`, `created_at`) VALUES
(1, 'erwe', 'werw', 'wer', NULL, '2025-01-22 16:16:48'),
(2, 'dsf', 'sdf', 'ds', NULL, '2025-01-22 16:17:48'),
(3, 'dfs', 'sdf', 'ds', NULL, '2025-01-22 16:18:01'),
(4, 'fgdf', 'dfg', 'dfg', NULL, '2025-01-22 16:18:12');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(3, 'Research Assistant'),
(2, 'Researcher');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role_id`) VALUES
(1, 'Researcher', 'researcher@example.com', '$2y$10$Awu5kxUcXcLABQVYkhB/pOYhDOTjWZQchnfrc7R9jsRQiU2gOHxAu', 2),
(2, 'Admin', 'admin@example.com', '$2y$10$mmfU2rT2g/1z0SmOwNLce.u75BtZzhm89ptroRGFMeIEArBVo/eZi', 1),
(3, 'Research Assistant', 'research_assistant@example.com', '$2y$10$Awu5kxUcXcLABQVYkhB/pOYhDOTjWZQchnfrc7R9jsRQiU2gOHxAu', 3),
(4, 'Research Assistant 2', 'research_assistant2@example.com', '$2y$10$Awu5kxUcXcLABQVYkhB/pOYhDOTjWZQchnfrc7R9jsRQiU2gOHxAu', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `equipmentusage`
--
ALTER TABLE `equipmentusage`
  ADD PRIMARY KEY (`usage_id`);

--
-- Indexes for table `projectassignments`
--
ALTER TABLE `projectassignments`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `researchers`
--
ALTER TABLE `researchers`
  ADD PRIMARY KEY (`researcher_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `equipmentusage`
--
ALTER TABLE `equipmentusage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `projectassignments`
--
ALTER TABLE `projectassignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `researchers`
--
ALTER TABLE `researchers`
  MODIFY `researcher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
