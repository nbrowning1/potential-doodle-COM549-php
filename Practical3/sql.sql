CREATE TABLE `UsersPracticals` (
 `UserID` int(11) NOT NULL,
 `Firstname` varchar(50) NOT NULL,
 `Surname` varchar(50) NOT NULL,
 `Username` varchar(50) NOT NULL,
 `Password` char(40) NOT NULL,
 `DateRegistered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `UserType` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Indexes for dumped tables
--
--
-- Indexes for table `UsersPracticals`
--
ALTER TABLE `UsersPracticals`
 ADD PRIMARY KEY (`UserID`);
--
-- AUTO_INCREMENT for dumped tables
--
--
-- AUTO_INCREMENT for table `UsersPracticals`
--
ALTER TABLE `UsersPracticals`
 MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;