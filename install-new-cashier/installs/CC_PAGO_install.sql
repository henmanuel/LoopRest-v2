-- ----------------------------------
-- caProcessor --
-- ----------------------------------
SET @processorId = 911;
SET @processorClassId = 1;
SET @processorIdParent = 270;
SET @processorIdClone = NULL;
SET @processorName = 'CC_PAGO';
SET @processorDisplayName = 'CC_PAGO';
SET @isActive = 1;
SET @typesSupported = 0;
SET @sortOrder = 0;
SET @isWithdraw = 0;
SET @isMenuHidden = 1;

INSERT INTO caProcessor (caProcessor_Id, caProcessorClass_Id, caProcessor_Id_Parent, caProcessor_Id_Clone, Name, DisplayName, IsActive, DateAdded, SortOrder, IsWithdraw, IsMenuHidden, IsCascading, InHouse, IsDesktop, IsTablet, IsMobile, TypesSupported, UnsuccessfulMax) 
VALUES (@processorId, @processorClassId, @processorIdParent, @processorIdClone, @processorName, @processorDisplayName, @isActive, NOW(), @sortOrder, @isWithdraw, @isMenuHidden, '0', '1', '1', '1', '1', @typesSupported, '5');

-- ----------------------------------
-- caProcessorSetting_Group --
-- ----------------------------------
INSERT INTO caProcessorSetting_Group (caProcessorSetting_Group_Id, Description, DateCreated) 
VALUES (NULL, @processorName, NOW());

SET @lastGroupId = LAST_INSERT_ID();

-- ----------------------------------
-- caProcessorSetting_Detail --
-- ----------------------------------
INSERT INTO caProcessorSetting_Detail (caProcessorSetting_Group_Id, Code, Value) 
VALUES
	(@lastGroupId, 'API_URL', 'https://secure.pagoglobal.com/transaction/execute');

-- ----------------------------------
-- caProcessorCompany --
-- ----------------------------------
INSERT INTO caProcessorCompany (caProcessor_Id, caCompany_Id, caProcessorSetting_Group_Id, IsActive, MinVIP, CreatedOn, Descriptor, AddRandomAmount) 
SELECT @processorId, co.caCompany_Id, @lastGroupId, 1,20, NOW(), 'Test Description', 1
FROM caCompany co
WHERE co.IsActive;

-- ----------------------------------------
-- caProcessorStep_HeaderProcessor --
-- ----------------------------------------
INSERT INTO caProcessorStep_HeaderProcessor (caProcessor_Id, caProcessorStep_Header_Id, caProcessorStep_Header_Id_W) 
VALUES (@processorId, '3', NULL);

-- ----------------------------------------
-- caProcessorSettingLabel --
-- ----------------------------------------
INSERT INTO caProcessorSettingLabel (caProcessorSettingLabelType_Id, caProcessor_Id, Label, Hide) 
VALUES 
	('1', @processorId, 'merchantID', '0'),
	('2', @processorId, 'passCode', '0'),
	('3', @processorId, NULL, '1'),
	('4', @processorId, NULL, '1');
	
-- ----------------------------------------
-- caProcessorMethodSupported --
-- ----------------------------------------
INSERT INTO caProcessorMethodSupported (caProcessor_Id, caProcessor_Id_Root) 
VALUES (@processorId, '11001'),
	   (@processorId, '11000');
	   
-- ----------------------------------
-- caErrorCodeHeader --
-- ----------------------------------
INSERT INTO caErrorCodeHeader(Name) 
VALUES ('PAGO');

SET @lastHeaderId = LAST_INSERT_ID();	   

-- ----------------------------------------
-- caErrorCodeHeaderProcessor --
-- ----------------------------------------
INSERT INTO caErrorCodeHeaderProcessor (caErrorCodeHeader_Id, caProcessor_Id) 
VALUES (@lastHeaderId, @processorId);

-- ----------------------------------------
-- caSoftwareProviderProcessorName --
-- ----------------------------------------	
INSERT INTO caSoftwareProviderProcessorName(caSoftwareProvider_Id,Name,Translate)
VALUES
	(24, @processorName, 'CC_PGCC'),
	(25, @processorName, 'CC_PGCC');