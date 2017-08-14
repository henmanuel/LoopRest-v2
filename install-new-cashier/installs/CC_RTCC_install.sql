-- ----------------------------------
-- caProcessor --
-- ----------------------------------
SET @processorId = 764;
SET @processorClassId = 1;
SET @processorIdParent = 270;
SET @processorIdClone = NULL;
SET @processorName = 'CC_RTCC2';
SET @processorDisplayName = 'CC_RTCC2';
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
	(@lastGroupId, 'URL_API', 'https://secure.qwipi.com/api/payments.jsp'),
	(@lastGroupId, 'MERCHANT_NO', '11682'),
	(@lastGroupId, 'LANGUAGE', 'ENG'),
	(@lastGroupId, 'MD5KEY', 'DA251F37E4C5EAE39AAC4B9F2CACDCA7'),
	(@lastGroupId, 'FormatReference', '[JOURNAL_ID]-[TRANS_ID]'),
	(@lastGroupId, 'FormatDescription', '[PROC_NAME]-[JOURNAL_ID]-[AMOUNT]-[TRANS_ID]');

-- ----------------------------------
-- caProcessorCompany --
-- ----------------------------------
INSERT INTO caProcessorCompany (caProcessor_Id, caCompany_Id, caProcessorSetting_Group_Id, IsActive, MinVIP, CreatedOn, Descriptor, AddRandomAmount) 
SELECT @processorId, co.caCompany_Id, @lastGroupId, 1,20, NOW(), NULL, 0
FROM caCompany co
WHERE co.IsActive;

-- ----------------------------------------
-- caProcessorStep_HeaderProcessor --
-- ----------------------------------------
INSERT INTO caProcessorStep_HeaderProcessor (caProcessor_Id, caProcessorStep_Header_Id, caProcessorStep_Header_Id_W) 
VALUES (@processorId, 1, '3');

-- ----------------------------------------
-- caProcessorSettingLabel --
-- ----------------------------------------
INSERT INTO caProcessorSettingLabel (caProcessorSettingLabelType_Id, caProcessor_Id, Label, Hide) 
VALUES 
	(1, @processorId, 'MerchantId', 0),
	(2, @processorId, 'ProfileId', 0),
	(3, @processorId, 'SiteName', 0),
	(4, @processorId, NULL, 1);

-- ----------------------------------------
-- caErrorCodeHeaderProcessor --
-- ----------------------------------------
INSERT INTO caErrorCodeHeaderProcessor (caErrorCodeHeader_Id, caProcessor_Id) 
VALUES (1, @processorId);