create table if not exists "Staff" (
    "staffID" varchar(50) primary key,
    "staffName" varchar(100) not null,
    "staffPhoneNo" varchar(15),
    "staffEmail" varchar(100) unique not null,
    "staffPassword" varchar(250) not null,
    "role" integer
);

create table if not exists "Donor" (
    "donorID" varchar(10) primary key,
    "donorName" varchar(100) not null,
    "donorPhoneNo" varchar(15),
    "donorDOB" date,
    "donorAddress" varchar(255),
    "donorEmail" varchar(100) unique not null,
    "donorPassword" varchar(250) not null
);

create table if not exists "Manager" (
    "managerID" varchar(10) primary key,
    "managerName" varchar(100) not null,
    "managerPhoneNo" varchar(15),
    "managerEmail" varchar(100) unique not null,
    "managerPassword" varchar(250) not null,
    "role" integer
);

create table if not exists "Allocation" (
    "allocationID" varchar(10) primary key,
    "allocationName" varchar(50) not null,
    "allocationStartDate" date not null,
    "allocationEndDate" date not null,
    "allocationStatus" varchar(50) not null,
    "allocationDetails" text,
    "targetAmount" double precision not null,
    "currentAmount" double precision not null,
    "allocationImage" varchar(255)
);

create table if not exists "Donation" (
    "donationID" varchar(10) primary key,
    "donationAmount" numeric(10, 2) not null,
    "donationDate" date not null,
    "donationMethod" varchar(50),
    "donationStatus" varchar(50),
    "donationReceipt" varchar(255),
    "donorID" varchar(10) references "Donor"("donorID"),
    "staffID" varchar(50) references "Staff"("staffID"),
    "allocationID" varchar(10) references "Allocation"("allocationID")
);

create table if not exists "Report" (
    "reportID" varchar(10) primary key,
    "reportType" varchar(100) not null,
    "reportDate" date not null,
    "managerID" varchar(10) references "Manager"("managerID"),
    "donationID" varchar(10) references "Donation"("donationID")
);
